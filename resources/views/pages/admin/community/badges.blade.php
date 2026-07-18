@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Badges" />
    @if(session('status'))<div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>@endif
    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <x-common.component-card title="Create Badge">
            <form method="POST" action="{{ route('admin.community.badges.store') }}" class="space-y-4">
                @csrf
                @foreach(['name' => 'Name', 'slug' => 'Slug', 'icon' => 'Icon', 'color' => 'Color', 'auto_assign_rule' => 'Auto Assign Rule'] as $field => $label)
                    <label><span class="mb-1.5 block text-sm text-[#A3A3A3]">{{ $label }}</span><input name="{{ $field }}" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30"></label>
                @endforeach
                <label><span class="mb-1.5 block text-sm text-[#A3A3A3]">Priority</span><input name="priority" type="number" value="10" min="0" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30"></label>
                <label><span class="mb-1.5 block text-sm text-[#A3A3A3]">Visibility</span><select name="visibility" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white"><option value="public">Public</option><option value="hidden">Hidden</option></select></label>
                <button class="wx-btn-primary px-5 py-3">Create Badge</button>
            </form>
        </x-common.component-card>

        <x-common.component-card title="Badges & Assignment">
            <div class="mb-6 flex flex-wrap gap-2">
                @foreach($badges as $badge)<x-community.badge :badge="$badge" />@endforeach
            </div>
            <form method="POST" action="{{ route('admin.community.badges.assign') }}" class="mb-6 grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                @csrf
                <select name="user_id" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
                    @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>@endforeach
                </select>
                <select name="badge_id" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
                    @foreach($badges as $badge)<option value="{{ $badge->id }}">{{ $badge->name }}</option>@endforeach
                </select>
                <button class="wx-btn-secondary px-5">Assign Badge</button>
            </form>
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.14em] text-[#6B7280]"><tr><th class="px-4 py-3">Badge</th><th class="px-4 py-3">Icon</th><th class="px-4 py-3">Color</th><th class="px-4 py-3">Priority</th><th class="px-4 py-3">Rule</th></tr></thead>
                    <tbody class="divide-y divide-white/[0.06]">
                        @foreach($badges as $badge)<tr><td class="px-4 py-3"><x-community.badge :badge="$badge" /></td><td class="px-4 py-3 text-[#A3A3A3]">{{ $badge->icon }}</td><td class="px-4 py-3 text-[#A3A3A3]">{{ $badge->color }}</td><td class="px-4 py-3 text-white">{{ $badge->priority }}</td><td class="px-4 py-3 text-[#A3A3A3]">{{ $badge->auto_assign_rule }}</td></tr>@endforeach
                    </tbody>
                </table>
            </div>

            <div class="grid gap-3 md:hidden">
                @foreach($badges as $badge)
                    <div class="rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                        <x-community.badge :badge="$badge" />
                        <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs text-[#A3A3A3]">
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Icon</dt><dd class="mt-0.5 text-white/80">{{ $badge->icon }}</dd></div>
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Color</dt><dd class="mt-0.5 text-white/80">{{ $badge->color }}</dd></div>
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Priority</dt><dd class="mt-0.5 text-white">{{ $badge->priority }}</dd></div>
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Rule</dt><dd class="mt-0.5 text-white/80">{{ $badge->auto_assign_rule ?: '-' }}</dd></div>
                        </dl>
                    </div>
                @endforeach
            </div>
        </x-common.component-card>
    </div>
@endsection
