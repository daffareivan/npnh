<?php

declare(strict_types=1);

namespace App\DTO\Roblox;

final readonly class AssetDTO
{
    public function __construct(
        public ?string $assetId,
        public string $status,
        public ?string $creatorUrl = null,
        public ?string $message = null,
    ) {}
}
