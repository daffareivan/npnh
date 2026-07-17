<?php

declare(strict_types=1);

namespace App\Services\Roblox;

use App\DTO\Roblox\UserDTO;

class RobloxUserService
{
    public function __construct(private readonly RobloxApiService $api) {}

    public function currentUser(string $accessToken): UserDTO
    {
        $profile = $this->api->authenticated($accessToken)
            ->get('/oauth/v1/userinfo')
            ->throw()
            ->json();

        return new UserDTO(
            id: (string) ($profile['sub'] ?? $profile['id']),
            username: $profile['preferred_username'] ?? $profile['name'] ?? null,
            displayName: $profile['name'] ?? $profile['nickname'] ?? null,
            avatarUrl: $profile['picture'] ?? null,
            metadata: $profile,
        );
    }
}
