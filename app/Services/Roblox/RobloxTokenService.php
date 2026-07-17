<?php

declare(strict_types=1);

namespace App\Services\Roblox;

use App\Models\RobloxAccount;

class RobloxTokenService
{
    public function tokenExpired(RobloxAccount $account): bool
    {
        return $account->expires_at !== null && $account->expires_at->isPast();
    }
}
