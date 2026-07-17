<?php

declare(strict_types=1);

namespace App\DTO\Roblox;

use Carbon\CarbonImmutable;

final readonly class OAuthDTO
{
    public function __construct(
        public string $accessToken,
        public ?string $refreshToken,
        public ?CarbonImmutable $expiresAt,
        public array $metadata = [],
    ) {}
}
