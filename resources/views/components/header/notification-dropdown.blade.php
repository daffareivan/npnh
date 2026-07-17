<div class="relative" x-data="{ dropdownOpen: false, notifying: {{ $notifications->isNotEmpty() ? 'true' : 'false' }} }" @click.away="dropdownOpen = false">
    <button class="wx-icon-button relative grid size-11 place-items-center rounded-full" @click="dropdownOpen = !dropdownOpen; notifying = false" type="button">
        <span x-show="notifying" class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-orange-400">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
        </span>
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8a6 6 0 0 0-12 0c0 4.499-1.411 5.956-2.738 7.326"/></svg>
    </button>

    <div x-show="dropdownOpen" x-transition class="wx-menu-popover absolute -right-[220px] z-50 mt-3 flex h-[420px] w-[350px] flex-col rounded-2xl p-3 sm:w-[380px] lg:right-0" style="display: none;">
        <div class="mb-3 flex items-center justify-between border-b pb-3" style="border-color: var(--border);">
            <div>
                <h5 class="text-lg font-semibold">{{ __('ui.notifications') }}</h5>
                <p class="text-xs" style="color: var(--muted-foreground);">{{ __('ui.latest_activity') }}</p>
            </div>
            <button @click="dropdownOpen = false" class="transition" style="color: var(--muted-foreground);" type="button">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            @forelse($notifications as $notification)
                <div class="wx-panel mb-2 flex gap-3 rounded-2xl p-3">
                    <span class="wx-pill mt-1 grid size-9 shrink-0 place-items-center rounded-full">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 12h16"/><path d="M12 4v16"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold">{{ $notification->event }}</p>
                        <p class="mt-1 text-xs" style="color: var(--muted-foreground);">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="wx-panel grid h-full place-items-center rounded-2xl p-6 text-center text-sm" style="color: var(--muted-foreground);">
                    {{ __('ui.no_recent_activity') }}
                </div>
            @endforelse
        </div>

        <a href="{{ route('admin.activity') }}" class="wx-menu-link mt-3 rounded-2xl p-3 text-center text-sm font-semibold">{{ __('ui.view_activity_log') }}</a>
    </div>
</div>
