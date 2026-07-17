<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback(CreditService $credits): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::query()
            ->where('provider', 'google')
            ->where('provider_id', $googleUser->getId())
            ->first();

        if (! $user) {
            $user = User::query()->where('email', $googleUser->getEmail())->first();
        }

        if ($user) {
            $user->forceFill([
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        } else {
            $user = User::query()->create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'WX User',
                'email' => $googleUser->getEmail(),
                'password' => Str::password(32),
                'avatar' => $googleUser->getAvatar(),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'role' => 'user',
            ]);
            $user->assignRole('user');
            $credits->grant($user, $credits->get(CreditService::REGISTRATION_BONUS), 'Registration Bonus');
        }

        if ($user->roles()->count() === 0) {
            $user->assignRole('user');
        }

        Auth::login($user, true);
        request()->session()->regenerate();
        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => request()->ip()])->save();

        return redirect()->intended($user->can('admin.access') ? route('admin.dashboard.show') : route('app.dashboard'));
    }
}
