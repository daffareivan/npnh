<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(private readonly SubscriptionService $subscriptions) {}

    public function create(User $user, Plan $plan, ?string $paymentMethod = null): Order
    {
        return Order::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'order_number' => 'NPNH-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
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
}
