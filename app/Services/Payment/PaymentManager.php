<?php

declare(strict_types=1);

namespace App\Services\Payment;

use InvalidArgumentException;

class PaymentManager
{
    public function __construct(
        private readonly MustikaPaymentService $mustika,
    ) {}

    public function gateway(?string $name = null): PaymentGatewayInterface
    {
        $gateway = $name ?: config('payment.default', 'mustika');

        return match ($gateway) {
            'mustika' => $this->mustika,
            default => throw new InvalidArgumentException("Unsupported payment gateway [{$gateway}]."),
        };
    }
}
