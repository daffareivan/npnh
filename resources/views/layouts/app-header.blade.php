@php
    $navigation = app(\App\Services\NavigationService::class);
    $navigationMenus = $navigation->adminTree(auth()->user());
    $searchItems = $navigationMenus->flatMap(fn ($section) => $section->children)->values();
    $quickActions = collect([
        ['label' => __('ui.open_converter'), 'href' => route('app.converter')],
        ['label' => __('ui.manage_users'), 'href' => route('admin.users.index')],
        ['label' => __('ui.create_plan'), 'href' => route('admin.subscription.plans')],
        ['label' => __('ui.credit_settings'), 'href' => route('admin.credit-settings.edit')],
        ['label' => __('ui.homepage_reviews'), 'href' => route('admin.content.homepage-reviews')],
        ['label' => __('ui.app_settings'), 'href' => route('admin.app-settings.edit')],
    ]);
@endphp

<header class="sticky top-0 z-99999 flex w-full border-b backdrop-blur-xl" style="background: color-mix(in srgb, var(--topbar) 82%, transparent); border-color: var(--border); color: var(--topbar-foreground);" x-data="{ mobileMenu: false, quickOpen: false, searchOpen: false, query: '' }">
    <div class="flex w-full flex-col items-center justify-between xl:flex-row xl:px-6">
        <div class="flex w-full items-center justify-between gap-2 border-b px-3 py-3 sm:gap-4 xl:justify-normal xl:border-b-0 xl:px-0 lg:py-4" style="border-color: var(--border);">
            <button class="wx-icon-button hidden size-11 items-center justify-center rounded-2xl xl:flex" @click="$store.sidebar.toggleExpanded()" aria-label="{{ __('ui.toggle_sidebar') }}">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16"/><path d="M4 12h10"/><path d="M4 17h16"/></svg>
            </button>

            <button class="wx-icon-button grid size-11 place-items-center rounded-2xl xl:hidden" @click="$store.sidebar.toggleMobileOpen()" aria-label="{{ __('ui.toggle_mobile_menu') }}">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/></svg>
            </button>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 xl:hidden">
                <span class="wx-pill grid size-10 place-items-center rounded-2xl">{!! \App\Helpers\MenuHelper::getIconSvg('audio-lines') !!}</span>
                <span class="font-semibold">NPNHCREATIVE</span>
            </a>

            <button @click="mobileMenu = !mobileMenu" class="wx-icon-button grid size-11 place-items-center rounded-2xl xl:hidden">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 12h.01"/><path d="M19 12h.01"/><path d="M5 12h.01"/></svg>
            </button>

            <div class="relative hidden xl:block" @click.away="searchOpen = false">
                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--muted-foreground);">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.34-4.34"/><circle cx="11" cy="11" r="8"/></svg>
                </span>
                <input x-model="query" @focus="searchOpen = true" type="text" placeholder="{{ __('ui.search_placeholder') }}" class="wx-field h-11 w-[460px] rounded-2xl py-2.5 pl-12 pr-4 text-sm outline-none">
                <div x-show="searchOpen" x-transition class="wx-menu-popover absolute left-0 z-50 mt-3 max-h-96 w-full overflow-y-auto rounded-2xl p-2" style="display: none;">
                    @foreach($searchItems as $item)
                        <a href="{{ $navigation->href($item) }}" x-show="query === '' || '{{ strtolower($navigation->title($item)) }}'.includes(query.toLowerCase())" class="wx-menu-link flex items-center gap-3 rounded-xl px-3 py-2 text-sm">
                            <span style="color: var(--foreground);">{!! \App\Helpers\MenuHelper::getIconSvg($item->icon ?: 'pages') !!}</span>
                            {{ $navigation->title($item) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div :class="mobileMenu ? 'flex' : 'hidden'" class="w-full items-center justify-between gap-4 px-5 py-4 shadow-theme-md xl:flex xl:w-auto xl:justify-end xl:px-0 xl:shadow-none">
            <div class="relative" @click.away="quickOpen = false">
                <button @click="quickOpen = !quickOpen" class="wx-icon-button rounded-2xl px-4 py-2.5 text-sm font-semibold">{{ __('ui.quick_actions') }}</button>
                <div x-show="quickOpen" x-transition class="wx-menu-popover absolute right-0 z-50 mt-3 w-56 rounded-2xl p-2" style="display: none;">
                    @foreach($quickActions as $action)
                        <a href="{{ $action['href'] }}" class="wx-menu-link block rounded-xl px-3 py-2 text-sm">{{ $action['label'] }}</a>
                    @endforeach
                </div>
            </div>

            @if($allowThemeSwitch ?? true)
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="wx-icon-button grid size-11 place-items-center rounded-full" type="button" aria-label="{{ __('ui.theme') }}">
                        <svg x-show="$store.theme.theme === 'light'" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                        <svg x-show="$store.theme.theme === 'dark'" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                        <svg x-show="$store.theme.theme === 'system'" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect width="18" height="12" x="3" y="4" rx="2"/><path d="M8 20h8"/><path d="M12 16v4"/></svg>
                    </button>
                    <div x-show="open" x-transition class="wx-menu-popover absolute right-0 z-50 mt-3 w-44 rounded-2xl p-2" style="display: none;">
                        @foreach(['light' => 'light', 'dark' => 'dark', 'system' => 'system'] as $themeValue => $labelKey)
                            <form method="POST" action="{{ route('preferences.theme') }}">
                                @csrf
                                <input type="hidden" name="theme" value="{{ $themeValue }}">
                                <button type="submit" @click="$store.theme.setTheme('{{ $themeValue }}')" class="wx-menu-link w-full rounded-xl px-3 py-2 text-left text-sm">{{ __("ui.$labelKey") }}</button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($allowLanguageSwitch ?? true)
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="wx-icon-button grid size-11 place-items-center rounded-full" type="button" aria-label="{{ __('ui.language') }}">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 0 20"/><path d="M12 2a15.3 15.3 0 0 0 0 20"/></svg>
                    </button>
                    <div x-show="open" x-transition class="wx-menu-popover absolute right-0 z-50 mt-3 w-44 rounded-2xl p-2" style="display: none;">
                        @foreach(['en' => 'english', 'id' => 'indonesia'] as $localeValue => $labelKey)
                            <form method="POST" action="{{ route('preferences.locale') }}">
                                @csrf
                                <input type="hidden" name="locale" value="{{ $localeValue }}">
                                <button type="submit" class="wx-menu-link w-full rounded-xl px-3 py-2 text-left text-sm {{ ($currentLocale ?? 'en') === $localeValue ? 'font-semibold' : '' }}" @if(($currentLocale ?? 'en') === $localeValue) style="background: color-mix(in srgb, var(--primary) 13%, transparent); color: var(--primary);" @endif>{{ __("ui.$labelKey") }}</button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif

            <x-header.notification-dropdown />
            <x-header.user-dropdown />
        </div>
    </div>
</header>
