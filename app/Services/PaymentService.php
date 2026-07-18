<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;

class PaymentService
{
    public function __construct(private readonly MidtransService $midtrans) {}

    public function checkoutUrl(Order $order): string
    {
        $transaction = $this->midtrans->createSnapTransaction($order);

        return $transaction->redirect_url ?? route('app.orders.show', $order);
    }

    public function providerName(): string
    {
        return 'midtrans';
    }
}
