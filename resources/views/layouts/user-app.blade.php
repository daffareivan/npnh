<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'NPNHCREATIVE' }}</title>
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
<body class="wx-shell min-h-screen antialiased">
    @php
        $navSubscription = auth()->user()?->activeSubscription?->loadMissing('plan');
        $navPlan = $navSubscription?->plan?->name ?? 'Free';
        $navCredits = auth()->user()?->credits_balance ?? 0;
        $navBadge = auth()->user()?->badges?->first(fn ($badge) => in_array($badge->slug, ['free', 'standard', 'premium', 'enterprise'], true));
    @endphp
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute left-1/2 top-[-20rem] h-[38rem] w-[38rem] -translate-x-1/2 rounded-full blur-3xl animate-pulse" style="background: color-mix(in srgb, var(--primary) 18%, transparent);"></div>
        <div class="absolute right-[-10rem] top-48 h-96 w-96 rounded-full blur-3xl" style="background: color-mix(in srgb, var(--foreground) 8%, transparent);"></div>
    </div>

    <header class="sticky top-0 z-40 border-b backdrop-blur-xl" style="background: color-mix(in srgb, var(--topbar) 82%, transparent); border-color: var(--border);">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('app.dashboard') }}" class="flex items-center gap-3">
                <span class="wx-pill grid size-9 place-items-center rounded-2xl">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 10v3"/><path d="M6 6v11"/><path d="M10 3v18"/><path d="M14 8v7"/><path d="M18 5v13"/><path d="M22 10v3"/></svg>
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
                <a href="{{ route('app.documentation') }}" class="transition hover:text-[var(--foreground)]">{{ __('navigation.documentation') }}</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('app.pricing') }}" class="wx-pill hidden rounded-full px-3 py-2 text-xs font-semibold sm:inline-flex">
                    {{ $navPlan }} <span class="mx-2 opacity-40">|</span> {{ $navCredits }} {{ __('common.credits') }}
                </a>
                @if($navBadge)
                    <x-community.badge :badge="$navBadge" class="hidden lg:inline-flex" />
                @endif
                <a href="{{ route('app.profile') }}" class="wx-pill grid size-9 place-items-center rounded-full text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-sm transition hover:text-[var(--foreground)]" style="color: var(--muted-foreground);">{{ __('ui.logout') }}</button></form>
            </div>
        </div>
    </header>

    <main class="relative z-10">
        @yield('content')
    </main>
</body>
</html>
