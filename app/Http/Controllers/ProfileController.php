<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        $user->fill($data)->save();

        return back()->with('status', 'Profile updated.');
    }

    public function password(PasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->forceFill(['password' => $request->validated()['password']])->save();

        return back()->with('status', 'Password updated.');
    }

    public function unlinkGoogle(Request $request): RedirectResponse
    {
        $request->user()->forceFill(['provider' => 'local', 'provider_id' => null])->save();

        return back()->with('status', 'Google account disconnected.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required']]);
        abort_unless(Hash::check($request->password, $request->user()->password), 403);

        $user = $request->user();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $user->delete();

        return redirect()->route('home');
    }
}
