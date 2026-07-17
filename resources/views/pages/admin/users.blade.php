@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Users" />
    <div class="overflow-hidden wx-card">
        <table class="min-w-full">
            <thead class="border-b border-white/[0.06]">
                <tr>
                    @foreach(['Avatar','Name','Email','Role','Status','Total Conversion','Created At','Action'] as $h)<th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-[0.14em] text-[#6B7280]">{{ $h }}</th>@endforeach
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
                        <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $user->audio_files_count }}</td>
                        <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-wrap gap-2">@csrf @method('PUT')
                                <input type="hidden" name="name" value="{{ $user->name }}"><input type="hidden" name="email" value="{{ $user->email }}"><input type="hidden" name="role" value="{{ $user->hasRole('Admin') ? 'Admin' : 'User' }}">
                                <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}">
                                <button class="text-warning-600">{{ $user->status === 'active' ? 'Suspend' : 'Activate' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-1">@csrf @method('DELETE')<button class="text-error-500">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $users->links() }}</div>
    </div>
@endsection
