<?php

declare(strict_types=1);

namespace App\Services\Roblox;

use App\DTO\Roblox\OAuthDTO;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RobloxOAuthService
{
    public function redirect(bool $forcePrompt = false): RedirectResponse
    {
        $state = Str::random(40);
        session(['roblox_oauth_state' => $state]);

        $parameters = [
            'client_id' => config('services.roblox.client_id'),
            'redirect_uri' => config('services.roblox.redirect'),
            'response_type' => 'code',
            'scope' => config('services.roblox.scopes', 'openid profile'),
            'state' => $state,
        ];

        if ($forcePrompt) {
            $parameters['prompt'] = 'login consent';
        }

        $url = config('services.roblox.authorize_url').'?'.http_build_query($parameters);

        return redirect()->away($url);
    }

    public function exchangeCode(string $code): OAuthDTO
    {
        $response = Http::asForm()
            ->withBasicAuth((string) config('services.roblox.client_id'), (string) config('services.roblox.client_secret'))
            ->post(config('services.roblox.token_url'), [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.roblox.redirect'),
            ])
            ->throw()
            ->json();

        return new OAuthDTO(
            accessToken: $response['access_token'],
            refreshToken: $response['refresh_token'] ?? null,
            expiresAt: isset($response['expires_in']) ? CarbonImmutable::now()->addSeconds((int) $response['expires_in']) : null,
            metadata: [
                'token_type' => $response['token_type'] ?? null,
                'scope' => $response['scope'] ?? null,
            ],
        );
    }
}
