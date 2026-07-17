<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\Roblox\AccountDTO;
use App\Models\RobloxAccount;
use App\Models\User;

class RobloxRepository
{
    public function upsertAccount(User $user, AccountDTO $account): RobloxAccount
    {
        return RobloxAccount::query()->updateOrCreate(
            ['user_id' => $user->id, 'provider' => 'roblox'],
            [
                'roblox_user_id' => $account->user->id,
                'username' => $account->user->username,
                'display_name' => $account->user->displayName,
                'avatar_url' => $account->user->avatarUrl,
                'access_token' => $account->oauth->accessToken,
                'refresh_token' => $account->oauth->refreshToken,
                'expires_at' => $account->oauth->expiresAt,
                'metadata' => [
                    'oauth' => $account->oauth->metadata,
                    'profile' => $account->user->metadata,
                ],
            ],
        );
    }
}
