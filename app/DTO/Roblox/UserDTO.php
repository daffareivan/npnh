<?php

declare(strict_types=1);

namespace App\DTO\Roblox;

final readonly class UserDTO
{
    public function __construct(
        public string $id,
        public ?string $username,
        public ?string $displayName,
        public ?string $avatarUrl = null,
        public array $metadata = [],
    ) {}
}
