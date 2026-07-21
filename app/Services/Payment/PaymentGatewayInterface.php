<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\Payment\DTO\PaymentCheckoutData;

interface PaymentGatewayInterface
{
    public function createPayment(Order $order): PaymentCheckoutData;

    public function checkStatus(Order $order): array;

    public function cancelPayment(Order $order): array;

    public function expirePayment(Order $order): array;

    public function handleWebhook(array $payload, array $headers = [], ?string $rawBody = null): PaymentTransaction;

    public function verifyWebhook(array $payload, array $headers = [], ?string $rawBody = null): bool;

    public function name(): string;
}
