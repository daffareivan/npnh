<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private readonly SubscriptionService $subscriptions) {}

    public function create(User $user, Plan $plan, ?string $paymentMethod = null): Order
    {
        return Order::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'order_number' => $this->nextOrderNumber(),
            'amount' => (int) $plan->price,
            'payment_method' => $paymentMethod,
            'payment_status' => $plan->price === 0 ? Order::STATUS_PENDING : Order::STATUS_WAITING_PAYMENT,
            'paid_at' => null,
        ]);
    }

    public function markPaid(Order $order, ?string $reference = null): Order
    {
        return DB::transaction(function () use ($order, $reference): Order {
            $locked = Order::query()->lockForUpdate()->with(['user', 'plan'])->findOrFail($order->id);

            if ($locked->payment_status !== Order::STATUS_PAID) {
                $locked->forceFill([
                    'payment_status' => Order::STATUS_PAID,
                    'transaction_reference' => $reference ?: $locked->transaction_reference,
                    'paid_at' => now(),
                ])->save();

                $this->subscriptions->activatePlan($locked->user, $locked->plan, 'order:'.$locked->order_number);
            }

            return $locked;
        });
    }

    private function nextOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $latest = Order::query()
            ->where('order_number', 'like', 'NPNH-'.$date.'-%')
            ->orderByDesc('order_number')
            ->value('order_number');

        $number = $latest ? ((int) substr($latest, -6)) + 1 : 1;

        return 'NPNH-'.$date.'-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }
}
