<?php

declare(strict_types=1);

namespace App\Services\Roblox;

use App\DTO\Roblox\AccountDTO;
use App\DTO\Roblox\OAuthDTO;
use App\Models\RobloxAccount;
use App\Models\User;
use App\Repositories\RobloxRepository;

class RobloxAccountService
{
    public function __construct(
        private readonly RobloxRepository $repository,
        private readonly RobloxUserService $users,
    ) {}

    public function connect(User $user, OAuthDTO $oauth): RobloxAccount
    {
        $robloxUser = $this->users->currentUser($oauth->accessToken);

        return $this->repository->upsertAccount($user, new AccountDTO($robloxUser, $oauth));
    }

    public function disconnect(User $user): void
    {
        $user->robloxAccount()->delete();
    }
}
