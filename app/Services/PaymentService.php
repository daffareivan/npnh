<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;

class PaymentService
{
    public function checkoutUrl(Order $order): string
    {
        return route('app.orders.show', $order);
    }

    public function providerName(): string
    {
        return 'manual';
    }
}
