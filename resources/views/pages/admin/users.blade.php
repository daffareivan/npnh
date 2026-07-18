@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Users" />
    @if(session('status'))<div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>@endif

    <div x-data="{
        createOpen: false,
        editOpen: false,
        editUser: { id: null, name: '', email: '', role: 'user', status: 'active', password: '' },
        deleteOpen: false,
        deleteUser: { id: null, name: '' },
        planOpen: false,
        planUser: { id: null, name: '', planId: null, customPlanName: '' },
        customPlanIds: [{{ $plans->where('is_custom', true)->pluck('id')->implode(',') }}],
        creditsOpen: false,
        creditsUser: { id: null, name: '', balance: 0 },
        openEdit(user) { this.editUser = { ...user, password: '' }; this.editOpen = true },
        openDelete(user) { this.deleteUser = user; this.deleteOpen = true },
        openPlan(user) { this.planUser = { customPlanName: '', ...user }; this.planOpen = true },
        openCredits(user) { this.creditsUser = { note: '', ...user }; this.creditsOpen = true },
    }" @keydown.escape.window="createOpen = false; editOpen = false; deleteOpen = false; planOpen = false; creditsOpen = false">

        <div class="mb-5 flex items-center justify-end">
            <button type="button" @click="createOpen = true" class="wx-btn-primary px-5 py-2.5 text-sm font-semibold">+ Add User</button>
        </div>

        <form class="wx-card mb-5 grid gap-3 p-3 sm:grid-cols-[1fr_160px_160px_auto]">
            <input name="search" value="{{ request('search') }}" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none placeholder:text-[#A3A3A3] focus:border-white/30" placeholder="Search name or email">
            <select name="role" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
                <option value="">All roles</option>
                @foreach(['Admin','User'] as $role)
                    <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                @endforeach
            </select>
            <select name="status" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
                <option value="">All status</option>
                @foreach(['active','suspended'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button class="wx-btn-secondary px-5 py-3">Filter</button>
        </form>

        <div class="hidden overflow-hidden wx-card lg:block">
            <table class="min-w-full">
                <thead class="border-b border-white/[0.06]">
                    <tr>
                        @foreach(['Avatar','Name','Email','Role','Status','Plan','Credits','Total Conversion','Created At','Action'] as $h)<th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-[0.14em] text-[#6B7280]">{{ $h }}</th>@endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.06]">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-5 py-4"><span class="grid size-10 place-items-center rounded-full bg-white text-sm font-semibold text-black">{{ strtoupper(substr($user->name, 0, 1)) }}</span></td>
                            <td class="px-5 py-4 text-sm font-medium text-white">{{ $user->name }}</td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $user->email }}</td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $user->roles->pluck('name')->join(', ') }}</td>
                            <td class="px-5 py-4"><span class="rounded-full px-2 py-1 text-xs {{ $user->status === 'active' ? 'bg-success-50 text-success-600' : 'bg-error-50 text-error-600' }}">{{ ucfirst($user->status) }}</span></td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $user->activeSubscription?->displayPlanName() ?? 'Free' }}</td>
                            <td class="px-5 py-4 text-sm font-medium text-white">{{ number_format($user->credits_balance) }}</td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $user->audio_files_count }}</td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-3">
                                    <button type="button" @click="openEdit({ id: {{ $user->id }}, name: @js($user->name), email: @js($user->email), role: @js($user->hasRole('admin') ? 'admin' : 'user'), status: @js($user->status) })" class="text-xs font-semibold text-brand-500">Edit</button>
                                    <button type="button" @click="openPlan({ id: {{ $user->id }}, name: @js($user->name), planId: {{ $user->activeSubscription?->plan_id ?? 'null' }}, customPlanName: @js($user->activeSubscription?->custom_plan_name ?? '') })" class="text-xs font-semibold text-blue-400">Change Plan</button>
                                    <button type="button" @click="openCredits({ id: {{ $user->id }}, name: @js($user->name), balance: {{ $user->credits_balance }} })" class="text-xs font-semibold text-amber-400">Set Credits</button>
                                    <button type="button" @click="openDelete({ id: {{ $user->id }}, name: @js($user->name) })" class="text-xs font-semibold text-error-500">Delete</button>
                                </div>
                                <form method="POST" action="{{ route('admin.users.credits.add', $user) }}" class="mt-2 flex items-center gap-1.5">
                                    @csrf
                                    <input type="number" name="amount" min="1" max="1000000" required placeholder="Amount" class="h-8 w-24 rounded-lg border border-white/[0.08] bg-black/20 px-2 text-xs text-white outline-none focus:border-white/30">
                                    <input type="text" name="note" maxlength="255" placeholder="Note (optional)" class="h-8 w-32 rounded-lg border border-white/[0.08] bg-black/20 px-2 text-xs text-white outline-none focus:border-white/30">
                                    <button class="text-success-500 text-xs font-semibold">Add Credit</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">{{ $users->links() }}</div>
        </div>

        <div class="grid gap-3 lg:hidden">
            @foreach($users as $user)
                <div class="wx-card p-4">
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-white text-sm font-semibold text-black">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-white">{{ $user->name }}</p>
                            <p class="truncate text-xs text-[#A3A3A3]">{{ $user->email }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-1 text-xs {{ $user->status === 'active' ? 'bg-success-50 text-success-600' : 'bg-error-50 text-error-600' }}">{{ ucfirst($user->status) }}</span>
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-x-3 gap-y-2 text-xs text-[#A3A3A3]">
                        <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Role</dt><dd class="mt-0.5 text-white/80">{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</dd></div>
                        <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Plan</dt><dd class="mt-0.5 text-white/80">{{ $user->activeSubscription?->displayPlanName() ?? 'Free' }}</dd></div>
                        <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Credits</dt><dd class="mt-0.5 font-medium text-white">{{ number_format($user->credits_balance) }}</dd></div>
                        <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Conversions</dt><dd class="mt-0.5 text-white/80">{{ $user->audio_files_count }}</dd></div>
                        <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Created</dt><dd class="mt-0.5 text-white/80">{{ $user->created_at->format('M d, Y') }}</dd></div>
                    </dl>
                    <div class="mt-4 flex flex-wrap gap-3 border-t border-white/[0.06] pt-3">
                        <button type="button" @click="openEdit({ id: {{ $user->id }}, name: @js($user->name), email: @js($user->email), role: @js($user->hasRole('admin') ? 'admin' : 'user'), status: @js($user->status) })" class="text-sm font-semibold text-brand-500">Edit</button>
                        <button type="button" @click="openPlan({ id: {{ $user->id }}, name: @js($user->name), planId: {{ $user->activeSubscription?->plan_id ?? 'null' }}, customPlanName: @js($user->activeSubscription?->custom_plan_name ?? '') })" class="text-sm font-semibold text-blue-400">Change Plan</button>
                        <button type="button" @click="openCredits({ id: {{ $user->id }}, name: @js($user->name), balance: {{ $user->credits_balance }} })" class="text-sm font-semibold text-amber-400">Set Credits</button>
                        <button type="button" @click="openDelete({ id: {{ $user->id }}, name: @js($user->name) })" class="text-sm font-semibold text-error-500">Delete</button>
                    </div>
                    <form method="POST" action="{{ route('admin.users.credits.add', $user) }}" class="mt-3 flex flex-wrap items-center gap-1.5 border-t border-white/[0.06] pt-3">
                        @csrf
                        <input type="number" name="amount" min="1" max="1000000" required placeholder="Amount" class="h-9 w-24 rounded-lg border border-white/[0.08] bg-black/20 px-2 text-xs text-white outline-none focus:border-white/30">
                        <input type="text" name="note" maxlength="255" placeholder="Note (optional)" class="h-9 min-w-0 flex-1 rounded-lg border border-white/[0.08] bg-black/20 px-2 text-xs text-white outline-none focus:border-white/30">
                        <button class="text-xs font-semibold text-success-500">Add Credit</button>
                    </form>
                </div>
            @endforeach
            <div class="wx-card p-4">{{ $users->links() }}</div>
        </div>

        {{-- Create User Modal --}}
        <div x-show="createOpen" x-cloak class="fixed inset-0 z-99999 flex items-end justify-center overflow-y-auto sm:items-center sm:p-5" style="display: none;">
            <div @click="createOpen = false" class="fixed inset-0 h-full w-full bg-black/60 backdrop-blur-sm" x-transition.opacity></div>
            <div @click.stop class="wx-card relative max-h-[90vh] w-full max-w-md overflow-y-auto p-6" x-transition>
                <h3 class="text-lg font-semibold text-white">Add User</h3>
                <p class="mt-1 text-xs text-[#6B7280]">New accounts are activated and email-verified immediately.</p>
                <form method="POST" action="{{ route('admin.users.store') }}" class="mt-5 flex flex-col gap-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Name</label>
                        <input name="name" value="{{ old('name') }}" required maxlength="255" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                        @error('name')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required maxlength="255" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                        @error('email')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Password</label>
                        <input type="password" name="password" required minlength="8" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                        @error('password')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Role</label>
                            <select name="role" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
                                <option value="user" @selected(old('role') === 'user')>User</option>
                                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Status</label>
                            <select name="status" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
                                <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                                <option value="suspended" @selected(old('status') === 'suspended')>Suspended</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="createOpen = false" class="wx-btn-secondary px-5 py-2.5 text-sm">Cancel</button>
                        <button type="submit" class="wx-btn-primary px-5 py-2.5 text-sm font-semibold">Create User</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit User Modal --}}
        <div x-show="editOpen" x-cloak class="fixed inset-0 z-99999 flex items-end justify-center overflow-y-auto sm:items-center sm:p-5" style="display: none;">
            <div @click="editOpen = false" class="fixed inset-0 h-full w-full bg-black/60 backdrop-blur-sm" x-transition.opacity></div>
            <div @click.stop class="wx-card relative max-h-[90vh] w-full max-w-md overflow-y-auto p-6" x-transition>
                <h3 class="text-lg font-semibold text-white">Edit User</h3>
                <p class="mt-1 text-xs text-[#6B7280]" x-text="editUser.name"></p>
                <form method="POST" :action="'{{ url('/admin/users') }}/' + editUser.id" class="mt-5 flex flex-col gap-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Name</label>
                        <input name="name" x-model="editUser.name" required maxlength="255" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Email</label>
                        <input type="email" name="email" x-model="editUser.email" required maxlength="255" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">New Password <span class="normal-case text-[#6B7280]">(leave blank to keep current)</span></label>
                        <input type="password" name="password" x-model="editUser.password" minlength="8" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Role</label>
                            <select name="role" x-model="editUser.role" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Status</label>
                            <select name="status" x-model="editUser.status" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="editOpen = false" class="wx-btn-secondary px-5 py-2.5 text-sm">Cancel</button>
                        <button type="submit" class="wx-btn-primary px-5 py-2.5 text-sm font-semibold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Plan Modal --}}
        <div x-show="planOpen" x-cloak class="fixed inset-0 z-99999 flex items-end justify-center overflow-y-auto sm:items-center sm:p-5" style="display: none;">
            <div @click="planOpen = false" class="fixed inset-0 h-full w-full bg-black/60 backdrop-blur-sm" x-transition.opacity></div>
            <div @click.stop class="wx-card relative w-full max-w-sm p-6" x-transition>
                <h3 class="text-lg font-semibold text-white">Change Plan</h3>
                <p class="mt-1 text-xs text-[#6B7280]" x-text="planUser.name"></p>
                <p class="mt-2 text-sm text-[#A3A3A3]">Switching plans immediately activates the new plan and grants its plan credits. The user's current plan is deactivated.</p>
                <form method="POST" :action="'{{ url('/admin/users') }}/' + planUser.id + '/plan'" class="mt-5 flex flex-col gap-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Plan</label>
                        <select name="plan_id" x-model="planUser.planId" required class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="customPlanIds.includes(Number(planUser.planId))" x-cloak>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Custom Plan Name <span class="normal-case text-[#6B7280]">(optional, shown instead of "Custom")</span></label>
                        <input type="text" name="custom_plan_name" x-model="planUser.customPlanName" maxlength="100" placeholder="e.g. Custom - Team ABC" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="planOpen = false" class="wx-btn-secondary px-5 py-2.5 text-sm">Cancel</button>
                        <button type="submit" class="wx-btn-primary px-5 py-2.5 text-sm font-semibold">Change Plan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Set Credits Modal --}}
        <div x-show="creditsOpen" x-cloak class="fixed inset-0 z-99999 flex items-end justify-center overflow-y-auto sm:items-center sm:p-5" style="display: none;">
            <div @click="creditsOpen = false" class="fixed inset-0 h-full w-full bg-black/60 backdrop-blur-sm" x-transition.opacity></div>
            <div @click.stop class="wx-card relative w-full max-w-sm p-6" x-transition>
                <h3 class="text-lg font-semibold text-white">Set Credit Balance</h3>
                <p class="mt-1 text-xs text-[#6B7280]" x-text="creditsUser.name"></p>
                <p class="mt-2 text-sm text-[#A3A3A3]">Overwrites the user's credit balance directly to this exact amount (recorded as an adjustment in their credit history).</p>
                <form method="POST" :action="'{{ url('/admin/users') }}/' + creditsUser.id + '/credits/set'" class="mt-5 flex flex-col gap-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">New Balance</label>
                        <input type="number" name="credits_balance" x-model="creditsUser.balance" min="0" max="100000000" required class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#A3A3A3]">Note <span class="normal-case text-[#6B7280]">(optional)</span></label>
                        <input type="text" name="note" x-model="creditsUser.note" maxlength="255" placeholder="Reason for adjustment" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="creditsOpen = false" class="wx-btn-secondary px-5 py-2.5 text-sm">Cancel</button>
                        <button type="submit" class="wx-btn-primary px-5 py-2.5 text-sm font-semibold">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete User Modal --}}
        <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-99999 flex items-end justify-center overflow-y-auto sm:items-center sm:p-5" style="display: none;">
            <div @click="deleteOpen = false" class="fixed inset-0 h-full w-full bg-black/60 backdrop-blur-sm" x-transition.opacity></div>
            <div @click.stop class="wx-card relative w-full max-w-sm p-6" x-transition>
                <h3 class="text-lg font-semibold text-white">Delete User</h3>
                <p class="mt-2 text-sm text-[#A3A3A3]">Are you sure you want to delete <span class="font-medium text-white" x-text="deleteUser.name"></span>? This action cannot be undone.</p>
                <form method="POST" :action="'{{ url('/admin/users') }}/' + deleteUser.id" class="mt-5 flex justify-end gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteOpen = false" class="wx-btn-secondary px-5 py-2.5 text-sm">Cancel</button>
                    <button type="submit" class="rounded-full bg-error-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-error-600">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection
