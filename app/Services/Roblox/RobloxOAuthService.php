<?php

declare(strict_types=1);

namespace App\Services\Roblox;

use App\DTO\Roblox\OAuthDTO;
use App\Models\RobloxAccount;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

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
            'prompt' => 'select_account',
        ];

        if ($forcePrompt) {
            $parameters['prompt'] = 'login consent select_account';
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

    public function accessTokenFor(RobloxAccount $account): string
    {
        if ($account->expires_at && $account->expires_at->lessThanOrEqualTo(now()->addMinute())) {
            $this->refreshAccount($account);
        }

        if (! $account->access_token) {
            throw new RuntimeException('Roblox session is missing. Please reconnect your Roblox account.');
        }

        return $account->access_token;
    }

    public function refreshAccount(RobloxAccount $account): RobloxAccount
    {
        if (! $account->refresh_token) {
            throw new RuntimeException('Roblox session expired. Please reconnect your Roblox account.');
        }

        $response = Http::asForm()
            ->withBasicAuth((string) config('services.roblox.client_id'), (string) config('services.roblox.client_secret'))
            ->post(config('services.roblox.token_url'), [
                'grant_type' => 'refresh_token',
                'refresh_token' => $account->refresh_token,
            ])
            ->throw()
            ->json();

        $metadata = $account->metadata ?? [];
        $metadata['oauth'] = array_merge($metadata['oauth'] ?? [], [
            'token_type' => $response['token_type'] ?? null,
            'scope' => $response['scope'] ?? ($metadata['oauth']['scope'] ?? null),
            'refreshed_at' => now()->toIso8601String(),
        ]);

        $account->forceFill([
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'] ?? $account->refresh_token,
            'expires_at' => isset($response['expires_in']) ? CarbonImmutable::now()->addSeconds((int) $response['expires_in']) : null,
            'metadata' => $metadata,
        ])->save();

        return $account->refresh();
    }
}
