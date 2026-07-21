<?php

declare(strict_types=1);

namespace App\Services\Payment\DTO;

final readonly class PaymentCheckoutData
{
    public function __construct(
        public string $reference,
        public ?string $gatewayTransactionId,
        public ?string $checkoutUrl,
        public string $status,
        public ?string $paymentMethod,
        public ?int $fee,
        public ?\DateTimeInterface $expiredAt,
        public array $payload,
    ) {}
}
