@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Users" />
    @if(session('status'))<div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>@endif
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
                    @foreach(['Avatar','Name','Email','Role','Status','Credits','Total Conversion','Created At','Action'] as $h)<th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-[0.14em] text-[#6B7280]">{{ $h }}</th>@endforeach
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
                        <td class="px-5 py-4 text-sm font-medium text-white">{{ number_format($user->credits_balance) }}</td>
                        <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $user->audio_files_count }}</td>
                        <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-wrap gap-2">@csrf @method('PUT')
                                <input type="hidden" name="name" value="{{ $user->name }}"><input type="hidden" name="email" value="{{ $user->email }}"><input type="hidden" name="role" value="{{ $user->hasRole('Admin') ? 'Admin' : 'User' }}">
                                <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                                <button class="text-warning-600">{{ $user->status === 'active' ? 'Suspend' : 'Activate' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-1">@csrf @method('DELETE')<button class="text-error-500">Delete</button></form>
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
                    <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Credits</dt><dd class="mt-0.5 font-medium text-white">{{ number_format($user->credits_balance) }}</dd></div>
                    <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Conversions</dt><dd class="mt-0.5 text-white/80">{{ $user->audio_files_count }}</dd></div>
                    <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Created</dt><dd class="mt-0.5 text-white/80">{{ $user->created_at->format('M d, Y') }}</dd></div>
                </dl>
                <div class="mt-4 flex flex-wrap gap-2 border-t border-white/[0.06] pt-3">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}"><input type="hidden" name="email" value="{{ $user->email }}"><input type="hidden" name="role" value="{{ $user->hasRole('Admin') ? 'Admin' : 'User' }}">
                        <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                        <button class="text-sm text-warning-600">{{ $user->status === 'active' ? 'Suspend' : 'Activate' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">@csrf @method('DELETE')<button class="text-sm text-error-500">Delete</button></form>
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
@endsection
