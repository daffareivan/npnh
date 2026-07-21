<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Payment\PaymentManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPaymentWebhook implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $gateway,
        private readonly array $payload,
        private readonly array $headers = [],
        private readonly ?string $rawBody = null,
    ) {}

    public function handle(PaymentManager $payments): void
    {
        $payments->gateway($this->gateway)->handleWebhook($this->payload, $this->headers, $this->rawBody);
    }
}
