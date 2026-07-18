<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.admin.users', [
            'title' => 'Users',
            'users' => User::query()
                ->with('roles')
                ->withCount('audioFiles')
                ->when($request->string('search')->toString(), function ($query, $search): void {
                    $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                })
                ->when($request->string('role')->toString(), fn ($query, $role) => $query->whereHas('roles', fn ($q) => $q->where('name', $role)))
                ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function addCredits(Request $request, User $user, CreditService $credits): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:1000000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $credits->grant($user, (int) $data['amount'], 'admin_manual_credit', 'success', null, [
            'note' => $data['note'] ?? null,
            'admin_id' => Auth::id(),
        ]);

        return back()->with('status', "Added {$data['amount']} credits to {$user->name}.");
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
