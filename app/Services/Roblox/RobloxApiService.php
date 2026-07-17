<?php

declare(strict_types=1);

namespace App\Services\Roblox;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class RobloxApiService
{
    public function authenticated(string $accessToken): PendingRequest
    {
        return Http::withToken($accessToken)
            ->acceptJson()
            ->baseUrl((string) config('services.roblox.api_url'));
    }
}
