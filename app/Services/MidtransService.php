<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\PaymentSuccessfulNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use RuntimeException;

class MidtransService
{
    public function __construct(private readonly OrderService $orders) {}

    public function createSnapTransaction(Order $order): object
    {
        $order->loadMissing(['user', 'plan']);
        $this->configure();

        $subscription = $this->ensurePendingSubscription($order);

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'item_details' => [[
                'id' => $order->plan->slug,
                'price' => (int) $order->amount,
                'quantity' => 1,
                'name' => $order->plan->name.' Plan - '.$order->plan->credits.' Credits',
            ]],
            'callbacks' => [
                'finish' => route('payment.success', ['order_id' => $order->order_number]),
                'unfinish' => route('payment.pending', ['order_id' => $order->order_number]),
                'error' => route('payment.failed', ['order_id' => $order->order_number]),
            ],
        ];

        $transaction = Snap::createTransaction($params);

        PaymentTransaction::query()->updateOrCreate(
            ['order_id' => $order->order_number],
            [
                'user_id' => $order->user_id,
                'subscription_id' => $subscription->id,
                'provider' => PaymentTransaction::PROVIDER_MIDTRANS,
                'gross_amount' => (int) $order->amount,
                'transaction_status' => 'pending',
                'raw_response' => [
                    'snap_token' => $transaction->token ?? null,
                    'redirect_url' => $transaction->redirect_url ?? null,
                ],
            ]
        );

        $this->log($order->user, $order, 'Create Payment', [
            'order_id' => $order->order_number,
            'amount' => $order->amount,
            'provider' => 'midtrans',
        ]);

        return $transaction;
    }

    public function generateOrderId(): string
    {
        return $this->nextNumber('NPNH', Order::class, 'order_number');
    }

    public function generateInvoiceNumber(): string
    {
        return $this->nextNumber('INV', Invoice::class, 'invoice_number');
    }

    public function verifySignature(array $payload): bool
    {
        $expected = hash(
            'sha512',
            ($payload['order_id'] ?? '').
            ($payload['status_code'] ?? '').
            ($payload['gross_amount'] ?? '').
            config('midtrans.server_key')
        );

        return hash_equals($expected, (string) ($payload['signature_key'] ?? ''));
    }

    public function handleNotification(array $payload): PaymentTransaction
    {
        if (! $this->verifySignature($payload)) {
            Log::warning('Invalid Midtrans signature', ['order_id' => $payload['order_id'] ?? null]);
            throw new RuntimeException('Invalid Midtrans signature.');
        }

        return DB::transaction(function () use ($payload): PaymentTransaction {
            $order = Order::query()
                ->where('order_number', $payload['order_id'] ?? '')
                ->with(['user', 'plan'])
                ->lockForUpdate()
                ->firstOrFail();

            $status = (string) ($payload['transaction_status'] ?? 'pending');
            $fraud = (string) ($payload['fraud_status'] ?? '');
            $isPaid = $status === 'settlement' || ($status === 'capture' && in_array($fraud, ['', 'accept'], true));

            $subscription = $this->ensurePendingSubscription($order);

            $transaction = PaymentTransaction::query()->updateOrCreate(
                ['order_id' => $order->order_number],
                [
                    'user_id' => $order->user_id,
                    'subscription_id' => $subscription->id,
                    'provider' => PaymentTransaction::PROVIDER_MIDTRANS,
                    'provider_transaction_id' => $payload['transaction_id'] ?? null,
                    'payment_type' => $payload['payment_type'] ?? null,
                    'gross_amount' => (int) round((float) ($payload['gross_amount'] ?? $order->amount)),
                    'transaction_status' => $status,
                    'fraud_status' => $payload['fraud_status'] ?? null,
                    'signature_key' => $payload['signature_key'] ?? null,
                    'raw_response' => $payload,
                    'paid_at' => $isPaid ? now() : null,
                ]
            );

            if ($isPaid) {
                $paidOrder = $this->orders->markPaid($order, $payload['transaction_id'] ?? null);
                $activeSubscription = $paidOrder->user->activeSubscription()->latest()->first();
                $invoice = $this->markInvoicePaid($paidOrder, $activeSubscription, $transaction);

                $transaction->forceFill(['subscription_id' => $activeSubscription?->id])->save();
                $activeSubscription?->forceFill([
                    'invoice_number' => $invoice->invoice_number,
                    'midtrans_order_id' => $paidOrder->order_number,
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
                    'amount' => (int) $paidOrder->amount,
                    'paid_at' => now(),
                ])->save();

                $this->log($paidOrder->user, $transaction, 'Payment Success', ['order_id' => $paidOrder->order_number]);
                $this->log($paidOrder->user, $activeSubscription, 'Subscription Activated', ['plan' => $paidOrder->plan->slug]);
                $this->log($paidOrder->user, $invoice, 'Invoice Generated', ['invoice' => $invoice->invoice_number]);
                $paidOrder->user->notify(new PaymentSuccessfulNotification($paidOrder, $invoice));
            } elseif (in_array($status, ['expire', 'cancel', 'deny'], true)) {
                $mapped = ['expire' => Order::STATUS_EXPIRED, 'cancel' => Order::STATUS_CANCELLED, 'deny' => 'denied'][$status];
                $order->forceFill(['payment_status' => $mapped])->save();
                $subscription->forceFill(['status' => $status === 'expire' ? 'expired' : 'cancelled'])->save();
                Invoice::query()->where('subscription_id', $subscription->id)->update(['status' => $status === 'expire' ? Invoice::STATUS_EXPIRED : Invoice::STATUS_CANCELLED]);
                $this->log($order->user, $transaction, 'Payment Failed', ['status' => $status]);
            }

            return $transaction;
        });
    }

    public function getTransactionStatus(string $orderId): object
    {
        $this->configure();

        return Transaction::status($orderId);
    }

    public function cancelTransaction(string $orderId): object
    {
        $this->configure();

        return Transaction::cancel($orderId);
    }

    public function expireTransaction(string $orderId): object
    {
        $this->configure();

        return Transaction::expire($orderId);
    }

    public function refundTransaction(string $orderId, array $params = []): object
    {
        $this->configure();

        return Transaction::refund($orderId, $params);
    }

    private function ensurePendingSubscription(Order $order): Subscription
    {
        $invoiceNumber = $this->invoiceNumberForOrder($order);

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

    private function markInvoicePaid(Order $order, ?Subscription $subscription, PaymentTransaction $transaction): Invoice
    {
        $invoiceNumber = $subscription?->invoice_number ?: $this->invoiceNumberForOrder($order);

        return Invoice::query()->updateOrCreate(
            ['invoice_number' => $invoiceNumber],
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

    private function invoiceNumberForOrder(Order $order): string
    {
        $suffix = str_replace('NPNH-', '', $order->order_number);

        return 'INV-'.$suffix;
    }

    private function nextNumber(string $prefix, string $model, string $column): string
    {
        $date = now()->format('Ymd');
        $latest = $model::query()
            ->where($column, 'like', $prefix.'-'.$date.'-%')
            ->orderByDesc($column)
            ->value($column);

        $number = $latest ? ((int) substr($latest, -6)) + 1 : 1;

        return $prefix.'-'.$date.'-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }

    private function configure(): void
    {
        Config::$serverKey = (string) config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');

        if (! Config::$serverKey) {
            throw new RuntimeException('Midtrans server key is not configured.');
        }
    }

    private function log(User $user, mixed $subject, string $event, array $properties = []): void
    {
        ActivityLog::query()->create([
            'user_id' => $user->id,
            'subject_type' => is_object($subject) && method_exists($subject, 'getMorphClass') ? $subject->getMorphClass() : null,
            'subject_id' => is_object($subject) && method_exists($subject, 'getKey') ? $subject->getKey() : null,
            'event' => $event,
            'properties' => $properties,
        ]);
    }
}
