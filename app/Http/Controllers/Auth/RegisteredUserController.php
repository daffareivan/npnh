<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('pages.auth.signup', ['title' => __('pages.register')]);
    }

    public function store(RegisterRequest $request, CreditService $credits): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'provider' => 'local',
            'role' => 'user',
        ]);
        $user->assignRole('user');
        $credits->grant($user, $credits->get(CreditService::REGISTRATION_BONUS), 'Registration Bonus');

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
