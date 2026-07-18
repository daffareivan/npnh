<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('pages.default') }} | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('npnhcreative_theme') || @json($currentTheme ?? 'system');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme === 'system' ? systemTheme : savedTheme;
            document.documentElement.dataset.theme = savedTheme;
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="wx-shell wx-app min-h-screen antialiased">
    @php
        $navSubscription = auth()->user()?->activeSubscription?->loadMissing('plan');
        $navPlan = $navSubscription?->plan?->name ?? 'Free';
        $navCredits = auth()->user()?->credits_balance ?? 0;
        $navBadge = auth()->user()?->badges?->first(fn ($badge) => in_array($badge->slug, ['free', 'standard', 'premium', 'enterprise'], true));
    @endphp
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="wx-decor-glow absolute left-1/2 top-[-20rem] h-[38rem] w-[38rem] -translate-x-1/2 rounded-full blur-3xl animate-pulse" style="background: color-mix(in srgb, var(--primary) 18%, transparent);"></div>
        <div class="wx-decor-glow absolute right-[-10rem] top-48 h-96 w-96 rounded-full blur-3xl" style="background: color-mix(in srgb, var(--foreground) 8%, transparent);"></div>
    </div>

    <header class="sticky top-0 z-40 border-b backdrop-blur-xl" style="background: color-mix(in srgb, var(--topbar) 82%, transparent); border-color: var(--border);" x-data="{
        menu: false,
        theme: localStorage.getItem('npnhcreative_theme') || @js($currentTheme ?? 'system'),
        setTheme(value) {
            this.theme = value;
            localStorage.setItem('npnhcreative_theme', value);
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const effectiveTheme = value === 'system' ? systemTheme : value;
            document.documentElement.dataset.theme = value;
            document.documentElement.classList.toggle('dark', effectiveTheme === 'dark');
        }
    }">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('app.dashboard') }}" class="flex items-center gap-3">
                <span class="grid size-9 place-items-center overflow-hidden rounded-full">
                    <img src="{{ asset('images/logo.png') }}" alt="NPNHCREATIVE" class="size-full object-contain">
                </span>
                <span class="font-semibold tracking-tight">NPNHCREATIVE</span>
            </a>
            <nav class="hidden items-center gap-6 text-sm md:flex" style="color: var(--muted-foreground);">
                <a href="{{ route('app.dashboard') }}" class="transition hover:text-[var(--foreground)]">{{ __('navigation.home') }}</a>
                <a href="{{ route('app.converter') }}" class="transition hover:text-[var(--foreground)]">{{ __('navigation.converter') }}</a>
                <a href="{{ route('app.history') }}" class="transition hover:text-[var(--foreground)]">{{ __('navigation.history') }}</a>
                <a href="{{ route('app.integrations') }}" class="transition hover:text-[var(--foreground)]">{{ __('navigation.integrations') }}</a>
                <a href="{{ route('app.integrations.settings') }}" class="transition hover:text-[var(--foreground)]">{{ __('navigation.settings') }}</a>
                <a href="{{ route('app.pricing') }}" class="transition hover:text-[var(--foreground)]">{{ __('navigation.pricing') }}</a>
                @if(auth()->user()?->can('admin.access'))
                    <a href="{{ route('admin.dashboard.show') }}" class="transition hover:text-[var(--foreground)]">{{ __('navigation.dashboard') }}</a>
                @endif
            </nav>
            <div class="flex items-center gap-2 sm:gap-3">
                @if($allowThemeSwitch ?? true)
                    <div class="relative hidden md:block" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="wx-icon-button grid size-9 place-items-center rounded-full" type="button" aria-label="{{ __('ui.theme') }}">
                            <svg x-show="theme === 'light'" class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                            <svg x-show="theme === 'dark'" class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                            <svg x-show="theme === 'system'" class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect width="18" height="12" x="3" y="4" rx="2"/><path d="M8 20h8"/><path d="M12 16v4"/></svg>
                        </button>
                        <div x-show="open" x-transition class="wx-menu-popover absolute right-0 z-50 mt-3 w-44 rounded-2xl p-2" style="display: none;">
                            @foreach(['light' => 'light', 'dark' => 'dark', 'system' => 'system'] as $themeValue => $labelKey)
                                <form method="POST" action="{{ route('preferences.theme') }}">
                                    @csrf
                                    <input type="hidden" name="theme" value="{{ $themeValue }}">
                                    <button type="submit" @click="setTheme('{{ $themeValue }}')" class="wx-menu-link w-full rounded-xl px-3 py-2 text-left text-sm" :style="theme === '{{ $themeValue }}' ? 'background: color-mix(in srgb, var(--primary) 13%, transparent); color: var(--primary); font-weight: 700;' : ''">{{ __("ui.$labelKey") }}</button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($allowLanguageSwitch ?? true)
                    <div class="relative hidden md:block" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="wx-icon-button grid size-9 place-items-center rounded-full" type="button" aria-label="{{ __('ui.language') }}">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 0 20"/><path d="M12 2a15.3 15.3 0 0 0 0 20"/></svg>
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

                <a href="{{ route('app.pricing') }}" class="wx-pill hidden rounded-full px-3 py-2 text-xs font-semibold sm:inline-flex">
                    {{ $navPlan }} <span class="mx-2 opacity-40">|</span> {{ $navCredits }} {{ __('common.credits') }}
                </a>
                @if($navBadge)
                    <x-community.badge :badge="$navBadge" class="hidden lg:inline-flex" />
                @endif
                <a href="{{ route('app.profile') }}" class="wx-pill grid size-9 place-items-center rounded-full text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</a>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">@csrf<button class="text-sm transition hover:text-[var(--foreground)]" style="color: var(--muted-foreground);">{{ __('ui.logout') }}</button></form>
                <button @click="menu = !menu" class="wx-icon-button grid size-9 place-items-center rounded-2xl md:hidden" aria-label="{{ __('ui.toggle_mobile_menu') }}">
                    <svg x-show="!menu" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/></svg>
                    <svg x-show="menu" x-cloak class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12"/><path d="M18 6 6 18"/></svg>
                </button>
            </div>
        </div>
        <div x-show="menu" x-cloak x-transition class="wx-menu-popover mx-4 mb-4 rounded-3xl p-4 md:hidden">
            <div class="grid gap-1 text-sm" style="color: var(--muted-foreground);">
                <a href="{{ route('app.dashboard') }}" class="rounded-2xl px-3 py-2.5 hover:bg-[var(--hover)] hover:text-[var(--foreground)]">{{ __('navigation.home') }}</a>
                <a href="{{ route('app.converter') }}" class="rounded-2xl px-3 py-2.5 hover:bg-[var(--hover)] hover:text-[var(--foreground)]">{{ __('navigation.converter') }}</a>
                <a href="{{ route('app.history') }}" class="rounded-2xl px-3 py-2.5 hover:bg-[var(--hover)] hover:text-[var(--foreground)]">{{ __('navigation.history') }}</a>
                <a href="{{ route('app.integrations') }}" class="rounded-2xl px-3 py-2.5 hover:bg-[var(--hover)] hover:text-[var(--foreground)]">{{ __('navigation.integrations') }}</a>
                <a href="{{ route('app.integrations.settings') }}" class="rounded-2xl px-3 py-2.5 hover:bg-[var(--hover)] hover:text-[var(--foreground)]">{{ __('navigation.settings') }}</a>
                <a href="{{ route('app.pricing') }}" class="rounded-2xl px-3 py-2.5 hover:bg-[var(--hover)] hover:text-[var(--foreground)]">{{ __('navigation.pricing') }}</a>
                @if(auth()->user()?->can('admin.access'))
                    <a href="{{ route('admin.dashboard.show') }}" class="rounded-2xl px-3 py-2.5 hover:bg-[var(--hover)] hover:text-[var(--foreground)]">{{ __('navigation.dashboard') }}</a>
                @endif
                <div class="my-2 h-px bg-white/10"></div>

                @if($allowThemeSwitch ?? true)
                    <div class="px-3 py-1.5 text-xs uppercase tracking-[0.14em]" style="color: var(--muted-foreground);">{{ __('ui.theme') }}</div>
                    <div class="mx-3 mb-2 grid grid-cols-3 gap-2">
                        @foreach(['light' => 'light', 'dark' => 'dark', 'system' => 'system'] as $themeValue => $labelKey)
                            <form method="POST" action="{{ route('preferences.theme') }}">
                                @csrf
                                <input type="hidden" name="theme" value="{{ $themeValue }}">
                                <button type="submit" @click="setTheme('{{ $themeValue }}')" class="wx-menu-link w-full rounded-2xl px-3 py-2 text-center text-xs" :style="theme === '{{ $themeValue }}' ? 'background: color-mix(in srgb, var(--primary) 13%, transparent); color: var(--primary); font-weight: 700;' : ''">{{ __("ui.$labelKey") }}</button>
                            </form>
                        @endforeach
                    </div>
                @endif

                @if($allowLanguageSwitch ?? true)
                    <div class="px-3 py-1.5 text-xs uppercase tracking-[0.14em]" style="color: var(--muted-foreground);">{{ __('ui.language') }}</div>
                    <div class="mx-3 mb-2 grid grid-cols-2 gap-2">
                        @foreach(['en' => 'english', 'id' => 'indonesia'] as $localeValue => $labelKey)
                            <form method="POST" action="{{ route('preferences.locale') }}">
                                @csrf
                                <input type="hidden" name="locale" value="{{ $localeValue }}">
                                <button type="submit" class="wx-menu-link w-full rounded-2xl px-3 py-2 text-center text-xs {{ ($currentLocale ?? 'en') === $localeValue ? 'font-semibold' : '' }}" @if(($currentLocale ?? 'en') === $localeValue) style="background: color-mix(in srgb, var(--primary) 13%, transparent); color: var(--primary);" @endif>{{ __("ui.$labelKey") }}</button>
                            </form>
                        @endforeach
                    </div>
                @endif

                <a href="{{ route('app.pricing') }}" class="wx-pill mx-3 inline-flex w-fit rounded-full px-3 py-2 text-xs font-semibold">
                    {{ $navPlan }} <span class="mx-2 opacity-40">|</span> {{ $navCredits }} {{ __('common.credits') }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">@csrf<button class="w-full rounded-2xl px-3 py-2.5 text-left transition hover:bg-[var(--hover)] hover:text-[var(--foreground)]">{{ __('ui.logout') }}</button></form>
            </div>
        </div>
    </header>

    <main class="relative z-10">
        @yield('content')
    </main>
</body>
</html>
