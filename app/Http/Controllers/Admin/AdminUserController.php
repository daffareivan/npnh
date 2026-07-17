<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.users', [
            'title' => 'Users',
            'users' => User::query()->with('roles')->withCount('audioFiles')->latest()->paginate(10),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,user'],
            'status' => ['required', 'in:active,suspended'],
            'password' => ['nullable', 'min:8'],
        ]);

        $user->fill($data);
        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        $user->forceFill(['role' => $data['role']])->save();
        $user->syncRoles([$data['role']]);

        return back();
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back();
    }
}
