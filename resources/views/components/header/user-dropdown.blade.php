@php
    $user = auth()->user();
    $plan = $user?->activeSubscription?->plan?->name ?? 'Free';
    $avatar = $user?->avatar ?: $user?->avatar_path;
@endphp

<div class="relative" x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false">
    <button class="wx-icon-button flex items-center gap-3 rounded-2xl px-3 py-2" @click.prevent="dropdownOpen = !dropdownOpen" type="button">
        <span class="grid size-9 place-items-center overflow-hidden rounded-full bg-gradient-to-br from-violet-500 to-blue-500 text-sm font-semibold text-white">
            @if($avatar)
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}" alt="" class="h-full w-full object-cover">
            @else
                {{ strtoupper(substr($user?->name ?? 'A', 0, 1)) }}
            @endif
        </span>
        <span class="hidden text-left xl:block">
            <span class="block text-sm font-semibold" style="color: var(--foreground);">{{ $user?->name ?? 'Admin' }}</span>
            <span class="block text-xs">{{ $user?->roles?->pluck('name')->implode(', ') ?: ($user?->role ?? 'admin') }}</span>
        </span>
        <svg class="size-4 transition-transform" :class="{ 'rotate-180': dropdownOpen }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <div x-show="dropdownOpen" x-transition class="wx-menu-popover absolute right-0 z-50 mt-3 w-72 rounded-2xl p-3" style="display: none;">
        <div class="wx-panel rounded-2xl p-4">
            <p class="font-semibold">{{ $user?->name ?? 'Admin' }}</p>
            <p class="mt-1 text-xs" style="color: var(--muted-foreground);">{{ $user?->email }}</p>
            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                <span class="wx-pill rounded-full px-2 py-1">{{ $plan }}</span>
                <span class="wx-pill rounded-full px-2 py-1">{{ number_format($user?->credits_balance ?? 0) }} Credits</span>
            </div>
        </div>

        <div class="mt-3 grid gap-1 text-sm">
            <a href="{{ route('admin.profile') }}" class="wx-menu-link rounded-xl px-3 py-2">{{ __('ui.profile') }}</a>
            <a href="{{ route('admin.app-settings.edit') }}" class="wx-menu-link rounded-xl px-3 py-2">{{ __('ui.settings') }}</a>
            <a href="{{ route('app.converter') }}" class="wx-menu-link rounded-xl px-3 py-2">{{ __('ui.open_converter') }}</a>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t pt-2" style="border-color: var(--border);">
            @csrf
            <button class="w-full rounded-xl px-3 py-2 text-left text-sm text-rose-200 transition hover:bg-rose-400/10" type="submit">{{ __('ui.logout') }}</button>
        </form>
    </div>
</div>
