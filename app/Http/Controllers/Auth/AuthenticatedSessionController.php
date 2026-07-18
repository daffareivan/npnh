<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('pages.auth.signin', ['title' => __('pages.login')]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'password']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials.']);
        }

        $request->session()->regenerate();
        $request->user()->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        ActivityLog::query()->create([
            'user_id' => $request->user()->id,
            'event' => 'login',
            'properties' => ['ip' => $request->ip()],
        ]);

        return redirect()->intended($request->user()->can('admin.access') ? route('admin.dashboard.show') : route('app.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        ActivityLog::query()->create([
            'user_id' => $request->user()?->id,
            'event' => 'logout',
            'properties' => ['ip' => $request->ip()],
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin');
    }
}
