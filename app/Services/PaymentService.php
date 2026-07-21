<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Services\Payment\PaymentManager;

class PaymentService
{
    public function __construct(private readonly PaymentManager $payments) {}

    public function checkoutUrl(Order $order): string
    {
        $transaction = $this->payments->gateway()->createPayment($order);

        return $transaction->checkoutUrl ?? route('app.orders.show', $order);
    }

    public function providerName(): string
    {
        return $this->payments->gateway()->name();
    }
}
