<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Notifications\PaymentSuccessfulNotification;
use App\Services\OrderService;
use App\Services\Payment\DTO\PaymentCheckoutData;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Process\Process;

class MustikaPaymentService implements PaymentGatewayInterface
{
    public function __construct(private readonly OrderService $orders) {}

    public function name(): string
    {
        return 'mustika';
    }

    public function createPayment(Order $order): PaymentCheckoutData
    {
        $order->loadMissing(['user', 'plan']);
        $subscription = $this->ensurePendingSubscription($order);

        $existing = PaymentTransaction::query()
            ->where('order_id', $order->order_number)
            ->where('payment_gateway', $this->name())
            ->whereIn('status', [PaymentTransaction::STATUS_PENDING, Order::STATUS_WAITING_PAYMENT])
            ->first();

        if ($existing && $existing->payment_reference && is_array($existing->raw_response)) {
            return $this->normalizeCheckout($existing->raw_response);
        }

        $payload = [
            'amount' => (int) $order->amount,
            'user' => $order->user->email,
            'merchant_id' => config('payment.mustika.merchant_id'),
            'invoice_number' => $order->order_number,
            'callback_url' => config('payment.mustika.callback_url'),
            'return_url' => route('payment.success', ['order_id' => $order->order_number]),
            'cancel_url' => route('payment.failed', ['order_id' => $order->order_number]),
        ];

        $response = $this->node('create_qris', $payload);
        $data = $this->normalizeCheckout($response);

        PaymentTransaction::query()->updateOrCreate(
            ['order_id' => $order->order_number],
            [
                'invoice_number' => $order->order_number,
                'payment_gateway' => $this->name(),
                'payment_reference' => $data->reference,
                'gateway_transaction_id' => $data->gatewayTransactionId,
                'user_id' => $order->user_id,
                'plan_id' => $order->plan_id,
                'subscription_id' => $subscription->id,
                'provider' => $this->name(),
                'provider_transaction_id' => $data->gatewayTransactionId,
                'payment_type' => $data->paymentMethod,
                'payment_method' => $data->paymentMethod,
                'gross_amount' => (int) $order->amount,
                'amount' => (int) $order->amount,
                'fee' => $data->fee ?? 0,
                'status' => PaymentTransaction::STATUS_PENDING,
                'transaction_status' => PaymentTransaction::STATUS_PENDING,
                'expired_at' => $data->expiredAt,
                'raw_response' => $data->payload,
            ]
        );

        $order->forceFill([
            'payment_method' => $this->name(),
            'transaction_reference' => $data->reference,
        ])->save();

        $this->paymentLog('create_payment', [
            'order_id' => $order->order_number,
            'amount' => $order->amount,
            'gateway' => $this->name(),
            'reference' => $data->reference,
        ]);

        return $data;
    }

    public function checkStatus(Order $order): array
    {
        $reference = $order->transaction_reference
            ?: PaymentTransaction::query()->where('order_id', $order->order_number)->value('payment_reference')
            ?: $order->order_number;

        return $this->node('check_qris_status', ['ref_no' => $reference]);
    }

    public function cancelPayment(Order $order): array
    {
        return $this->markTerminal($order, PaymentTransaction::STATUS_CANCELLED);
    }

    public function expirePayment(Order $order): array
    {
        return $this->markTerminal($order, PaymentTransaction::STATUS_EXPIRED);
    }

    public function handleWebhook(array $payload, array $headers = [], ?string $rawBody = null): PaymentTransaction
    {
        if (! $this->verifyWebhook($payload, $headers, $rawBody)) {
            throw new RuntimeException('Invalid Mustika Payment webhook signature.');
        }

        return DB::transaction(function () use ($payload): PaymentTransaction {
            $orderNumber = (string) ($payload['invoice_number'] ?? $payload['order_id'] ?? $payload['merchant_ref'] ?? '');
            $reference = (string) ($payload['ref_no'] ?? $payload['reference'] ?? $payload['payment_reference'] ?? '');

            $order = Order::query()
                ->when($orderNumber !== '', fn ($query) => $query->where('order_number', $orderNumber))
                ->when($orderNumber === '' && $reference !== '', fn ($query) => $query->where('transaction_reference', $reference))
                ->with(['user', 'plan'])
                ->lockForUpdate()
                ->firstOrFail();

            $this->validatePayload($order, $payload);

            $subscription = $this->ensurePendingSubscription($order);
            $status = $this->mapStatus($payload['status'] ?? $payload['transaction_status'] ?? null);
            $transactionId = (string) ($payload['transaction_id'] ?? $payload['gateway_transaction_id'] ?? $reference ?: $order->transaction_reference);

            $transaction = PaymentTransaction::query()->updateOrCreate(
                ['order_id' => $order->order_number],
                [
                    'invoice_number' => $order->order_number,
                    'payment_gateway' => $this->name(),
                    'payment_reference' => $reference ?: $order->transaction_reference,
                    'gateway_transaction_id' => $transactionId,
                    'user_id' => $order->user_id,
                    'plan_id' => $order->plan_id,
                    'subscription_id' => $subscription->id,
                    'provider' => $this->name(),
                    'provider_transaction_id' => $transactionId,
                    'payment_type' => $payload['payment_method'] ?? $payload['type'] ?? null,
                    'payment_method' => $payload['payment_method'] ?? $payload['type'] ?? null,
                    'gross_amount' => (int) $order->amount,
                    'amount' => (int) ($payload['amount'] ?? $order->amount),
                    'fee' => (int) ($payload['fee'] ?? 0),
                    'status' => $status,
                    'transaction_status' => $status,
                    'callback_payload' => $payload,
                    'raw_response' => $payload,
                    'paid_at' => $status === PaymentTransaction::STATUS_PAID ? now() : null,
                ]
            );

            if ($status === PaymentTransaction::STATUS_PAID && $order->payment_status !== Order::STATUS_PAID) {
                $paidOrder = $this->orders->markPaid($order, $transactionId);
                $activeSubscription = $paidOrder->user->activeSubscription()->latest()->first();
                $invoice = $this->markInvoicePaid($paidOrder, $activeSubscription);

                $transaction->forceFill(['subscription_id' => $activeSubscription?->id])->save();
                $paidOrder->user->notify(new PaymentSuccessfulNotification($paidOrder, $invoice));
                $this->activity($paidOrder, $transaction, 'Payment Success');
            }

            if (in_array($status, [PaymentTransaction::STATUS_FAILED, PaymentTransaction::STATUS_EXPIRED, PaymentTransaction::STATUS_CANCELLED], true)) {
                $order->forceFill(['payment_status' => $this->orderStatus($status)])->save();
                $subscription->forceFill(['status' => $status === PaymentTransaction::STATUS_EXPIRED ? 'expired' : 'cancelled'])->save();
                Invoice::query()->where('subscription_id', $subscription->id)->update(['status' => $this->invoiceStatus($status)]);
            }

            $this->paymentLog('webhook_processed', [
                'order_id' => $order->order_number,
                'status' => $status,
                'reference' => $reference,
            ]);

            return $transaction;
        });
    }

    public function verifyWebhook(array $payload, array $headers = [], ?string $rawBody = null): bool
    {
        $merchantId = config('payment.mustika.merchant_id');
        $payloadMerchant = $payload['merchant_id'] ?? $payload['merchant'] ?? null;

        if ($merchantId && $payloadMerchant && ! hash_equals((string) $merchantId, (string) $payloadMerchant)) {
            return false;
        }

        $secret = (string) config('payment.mustika.callback_secret');
        if ($secret === '') {
            return true;
        }

        $signature = $this->header($headers, 'x-signature')
            ?? $this->header($headers, 'x-callback-signature')
            ?? $payload['signature']
            ?? null;

        if (! $signature) {
            return false;
        }

        try {
            $result = $this->node('verify_callback', [
                'body' => $rawBody ?: json_encode($payload, JSON_UNESCAPED_SLASHES),
                'signature' => $signature,
            ]);

            return (bool) ($result['valid'] ?? false);
        } catch (RuntimeException) {
            return false;
        }
    }

    private function node(string $action, array $payload = []): array
    {
        $this->ensureConfigured();

        $this->paymentLog('node_request', [
            'action' => $action,
            'payload' => Arr::except($payload, ['apiKey', 'secret']),
        ]);

        $process = new Process([
            $this->nodeBinary(),
            base_path('resources/js/mustika-payment-bridge.cjs'),
        ], base_path(), [
            'APP_DEBUG' => config('app.debug') ? 'true' : 'false',
            'MUSTIKA_RESOLVED_IP' => (string) config('payment.mustika.resolved_ip'),
        ], json_encode([
            'action' => $action,
            'apiKey' => config('payment.mustika.api_key'),
            'baseUrl' => config('payment.mustika.base_url'),
            'timeout' => ((int) config('payment.mustika.timeout', 30)) * 1000,
            'payload' => $payload,
        ], JSON_THROW_ON_ERROR), (float) config('payment.mustika.timeout', 30));

        $process->run();

        $decoded = json_decode($process->getOutput(), true);

        if (! $process->isSuccessful() || ! is_array($decoded) || ($decoded['ok'] ?? false) !== true) {
            $message = is_array($decoded) ? ($decoded['message'] ?? 'MustikaPay Node bridge failed.') : trim($process->getErrorOutput());

            throw new RuntimeException($message ?: 'MustikaPay Node bridge failed.');
        }

        $result = $decoded['result'] ?? [];
        if (($result['status'] ?? null) === 'error') {
            $this->paymentLog('node_error', [
                'action' => $action,
                'response' => $result,
            ]);

            throw new RuntimeException($this->friendlyGatewayError((string) ($result['message'] ?? 'MustikaPay returned an error.')));
        }

        $this->paymentLog('node_response', [
            'action' => $action,
            'response' => $result,
        ]);

        return is_array($result) ? $result : ['result' => $result];
    }

    private function normalizeCheckout(array $response): PaymentCheckoutData
    {
        $data = $response['data'] ?? $response;
        $reference = (string) ($data['ref_no'] ?? $data['reference'] ?? $data['payment_reference'] ?? $data['trx_id'] ?? '');

        if ($reference === '') {
            throw new RuntimeException('Mustika Payment did not return a payment reference.');
        }

        return new PaymentCheckoutData(
            reference: $reference,
            gatewayTransactionId: $data['transaction_id'] ?? $data['trx_id'] ?? null,
            checkoutUrl: $data['payment_link'] ?? $data['checkout_url'] ?? $data['qr_url'] ?? null,
            status: PaymentTransaction::STATUS_PENDING,
            paymentMethod: $data['payment_method'] ?? $data['method'] ?? 'qris',
            fee: isset($data['fee']) ? (int) $data['fee'] : null,
            expiredAt: isset($data['expired_at']) ? CarbonImmutable::parse($data['expired_at']) : now()->addDay(),
            payload: $response,
        );
    }

    private function ensurePendingSubscription(Order $order): Subscription
    {
        $invoiceNumber = $order->order_number;

        $subscription = Subscription::query()->firstOrCreate(
            ['midtrans_order_id' => $order->order_number],
            [
                'user_id' => $order->user_id,
                'plan_id' => $order->plan_id,
                'invoice_number' => $invoiceNumber,
                'status' => 'pending',
                'amount' => (int) $order->amount,
                'started_at' => null,
            ]
        );

        Invoice::query()->firstOrCreate(
            ['invoice_number' => $invoiceNumber],
            [
                'user_id' => $order->user_id,
                'subscription_id' => $subscription->id,
                'status' => Invoice::STATUS_PENDING,
                'subtotal' => (int) $order->amount,
                'tax' => 0,
                'total' => (int) $order->amount,
            ]
        );

        return $subscription;
    }

    private function markInvoicePaid(Order $order, ?Subscription $subscription): Invoice
    {
        return Invoice::query()->updateOrCreate(
            ['invoice_number' => $order->order_number],
            [
                'user_id' => $order->user_id,
                'subscription_id' => $subscription?->id,
                'status' => Invoice::STATUS_PAID,
                'subtotal' => (int) $order->amount,
                'tax' => 0,
                'total' => (int) $order->amount,
                'paid_at' => now(),
            ]
        );
    }

    private function validatePayload(Order $order, array $payload): void
    {
        if (isset($payload['amount']) && (int) $payload['amount'] !== (int) $order->amount) {
            throw new RuntimeException('Invalid Mustika Payment amount.');
        }
    }

    private function mapStatus(mixed $status): string
    {
        return match (strtolower((string) $status)) {
            'success', 'paid', 'settlement', 'capture', 'completed', 'berhasil' => PaymentTransaction::STATUS_PAID,
            'expired', 'expire' => PaymentTransaction::STATUS_EXPIRED,
            'cancelled', 'canceled', 'cancel' => PaymentTransaction::STATUS_CANCELLED,
            'failed', 'deny', 'denied', 'error', 'gagal' => PaymentTransaction::STATUS_FAILED,
            'refunded', 'refund' => PaymentTransaction::STATUS_REFUNDED,
            default => PaymentTransaction::STATUS_PENDING,
        };
    }

    private function markTerminal(Order $order, string $status): array
    {
        DB::transaction(function () use ($order, $status): void {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);
            $locked->forceFill(['payment_status' => $this->orderStatus($status)])->save();

            PaymentTransaction::query()->where('order_id', $locked->order_number)->update([
                'status' => $status,
                'transaction_status' => $status,
            ]);
        });

        return ['status' => $status];
    }

    private function orderStatus(string $status): string
    {
        return match ($status) {
            PaymentTransaction::STATUS_PAID => Order::STATUS_PAID,
            PaymentTransaction::STATUS_EXPIRED => Order::STATUS_EXPIRED,
            PaymentTransaction::STATUS_CANCELLED => Order::STATUS_CANCELLED,
            PaymentTransaction::STATUS_REFUNDED => Order::STATUS_REFUNDED,
            default => 'failed',
        };
    }

    private function invoiceStatus(string $status): string
    {
        return match ($status) {
            PaymentTransaction::STATUS_EXPIRED => Invoice::STATUS_EXPIRED,
            PaymentTransaction::STATUS_CANCELLED => Invoice::STATUS_CANCELLED,
            default => Invoice::STATUS_CANCELLED,
        };
    }

    private function ensureConfigured(): void
    {
        if (! config('payment.mustika.api_key')) {
            throw new RuntimeException('Mustika Payment API key is not configured.');
        }
    }

    private function nodeBinary(): string
    {
        return (string) env('NODE_BINARY', 'node');
    }

    private function friendlyGatewayError(string $message): string
    {
        if (str_contains(strtolower($message), 'gagal terhubung')) {
            return 'Gagal terhubung ke server MustikaPay. Pastikan IP server sudah masuk Whitelist IP Address di dashboard MustikaPay, API key benar, dan server bisa mengakses mustikapayment.com.';
        }

        return $message;
    }

    private function header(array $headers, string $name): ?string
    {
        foreach ($headers as $key => $value) {
            if (strtolower((string) $key) === $name) {
                return is_array($value) ? (string) ($value[0] ?? '') : (string) $value;
            }
        }

        return null;
    }

    private function paymentLog(string $event, array $context = []): void
    {
        Log::channel('payment')->info($event, $context);
    }

    private function activity(Order $order, PaymentTransaction $transaction, string $event): void
    {
        ActivityLog::query()->create([
            'user_id' => $order->user_id,
            'subject_type' => $transaction->getMorphClass(),
            'subject_id' => $transaction->id,
            'event' => $event,
            'properties' => ['order_id' => $order->order_number, 'gateway' => $this->name()],
        ]);
    }
}
