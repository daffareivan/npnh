<?php

declare(strict_types=1);

namespace App\DTO\Roblox;

final readonly class AccountDTO
{
    public function __construct(
        public UserDTO $user,
        public OAuthDTO $oauth,
    ) {}
}
