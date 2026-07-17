<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RobloxAccount;
use App\Models\User;

class RobloxAccountPolicy
{
    public function view(User $user, RobloxAccount $account): bool
    {
        return $account->user_id === $user->id;
    }

    public function delete(User $user, RobloxAccount $account): bool
    {
        return $account->user_id === $user->id;
    }
}
