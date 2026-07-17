<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth {{ $introAnimationEnabled ? '' : 'wx-page-ready' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ __('homepage.meta_description') }}">
    <link rel="canonical" href="{{ route('home') }}">
    <meta property="og:title" content="{{ __('homepage.meta_title') }}">
    <meta property="og:description" content="{{ __('homepage.og_description') }}">
    <meta property="og:url" content="{{ route('home') }}">
    <meta property="og:type" content="website">
    <title>{{ __('homepage.meta_title') }}</title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('npnhcreative_theme') || @json($currentTheme ?? 'system');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const effectiveTheme = savedTheme === 'system' ? systemTheme : savedTheme;
            document.documentElement.dataset.theme = savedTheme;
            document.documentElement.classList.toggle('dark', effectiveTheme === 'dark');
        })();
    </script>
    <script>
        (() => {
            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const shown = sessionStorage.getItem('npnhcreative_intro_shown') === '1';
            if (reduced || shown || @json(! $introAnimationEnabled)) {
                document.documentElement.classList.add('wx-page-ready');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "NPNHCREATIVE",
            "applicationCategory": "MultimediaApplication",
            "operatingSystem": "Web",
            "description": "{{ __('homepage.schema_description') }}",
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "{{ number_format($reviewSummary['average'], 1) }}",
                "reviewCount": "{{ $reviewSummary['count'] }}"
            },
            "review": @json($reviewSchema)
        }
    </script>
</head>
<body class="wx-shell wx-marketing min-h-screen antialiased" x-data="{
    menu: false,
    scrolled: false,
    theme: localStorage.getItem('npnhcreative_theme') || @js($currentTheme ?? 'system'),
    setTheme(value) {
        this.theme = value;
        localStorage.setItem('npnhcreative_theme', value);
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const effectiveTheme = value === 'system' ? systemTheme : value;
        document.documentElement.dataset.theme = value;
        document.documentElement.classList.toggle('dark', effectiveTheme === 'dark');
    }
}" @scroll.window="scrolled = window.scrollY > 16">
    <x-brand-intro :enabled="$introAnimationEnabled" />
    <div class="wx-page-shell">
    <header class="fixed inset-x-0 top-0 z-50 transition-all duration-300" :class="scrolled ? 'border-b shadow-2xl backdrop-blur-xl' : 'bg-transparent'" :style="scrolled ? 'background: color-mix(in srgb, var(--topbar) 82%, transparent); border-color: var(--border); box-shadow: 0 18px 60px var(--shadow);' : ''">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="group flex items-center gap-3">
                <span class="wx-pill grid size-10 place-items-center rounded-2xl transition">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 11v2"/><path d="M7 7v10"/><path d="M11 4v16"/><path d="M15 8v8"/><path d="M19 10v4"/></svg>
                </span>
                <span class="text-base font-semibold tracking-tight">NPNHCREATIVE</span>
            </a>

            <div class="hidden items-center gap-7 text-sm md:flex" style="color: var(--muted-foreground);">
                @foreach(__('homepage.nav') as $key => $label)
                    @php($href = $key === 'documentation' ? route('app.documentation') : '#'.($key === 'home' ? 'home' : $key))
                    <a href="{{ $href }}" class="transition hover:text-[var(--foreground)]">{{ $label }}</a>
                @endforeach
            </div>

            <div class="hidden items-center gap-3 md:flex">
                @if($allowThemeSwitch ?? true)
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="wx-icon-button grid size-10 place-items-center rounded-full" type="button" aria-label="{{ __('ui.theme') }}">
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
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="wx-icon-button grid size-10 place-items-center rounded-full" type="button" aria-label="{{ __('ui.language') }}">
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

                <a href="{{ route('signin') }}" class="wx-btn-secondary px-4 py-2.5 text-sm">{{ __('homepage.login') }}</a>
                <a href="{{ route('signup') }}" class="wx-btn-primary px-4 py-2.5 text-sm">{{ __('homepage.get_started') }}</a>
            </div>

            <button class="wx-icon-button grid size-10 place-items-center rounded-2xl md:hidden" @click="menu = !menu" aria-label="Open menu">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/></svg>
            </button>
        </nav>
        <div x-show="menu" x-transition class="wx-menu-popover mx-4 mb-4 rounded-3xl p-4 md:hidden">
            <div class="grid gap-2 text-sm" style="color: var(--muted-foreground);">
                @foreach(__('homepage.nav') as $key => $label)
                    @php($href = $key === 'documentation' ? route('app.documentation') : '#'.($key === 'home' ? 'home' : $key))
                    <a href="{{ $href }}" class="rounded-2xl px-3 py-2 hover:bg-[var(--hover)] hover:text-[var(--foreground)]">{{ $label }}</a>
                @endforeach
                @if(($allowThemeSwitch ?? true) || ($allowLanguageSwitch ?? true))
                    <div class="my-2 h-px bg-white/10"></div>
                @endif
                @if($allowThemeSwitch ?? true)
                    <div class="grid grid-cols-3 gap-2">
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
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['en' => 'english', 'id' => 'indonesia'] as $localeValue => $labelKey)
                            <form method="POST" action="{{ route('preferences.locale') }}">
                                @csrf
                                <input type="hidden" name="locale" value="{{ $localeValue }}">
                                <button type="submit" class="wx-menu-link w-full rounded-2xl px-3 py-2 text-center text-xs {{ ($currentLocale ?? 'en') === $localeValue ? 'font-semibold' : '' }}" @if(($currentLocale ?? 'en') === $localeValue) style="background: color-mix(in srgb, var(--primary) 13%, transparent); color: var(--primary);" @endif>{{ __("ui.$labelKey") }}</button>
                            </form>
                        @endforeach
                    </div>
                @endif
                <a href="{{ route('signup') }}" class="wx-btn-primary mt-2 px-4 py-3 text-center">{{ __('homepage.get_started') }}</a>
            </div>
        </div>
    </header>

    <main id="home" class="overflow-hidden">
        <section class="relative mx-auto grid min-h-screen max-w-7xl items-center gap-12 px-4 pb-20 pt-32 sm:px-6 lg:grid-cols-[1fr_500px] lg:px-8">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-[44rem]" style="background: radial-gradient(circle at 50% 0%, color-mix(in srgb, var(--primary) 13%, transparent), transparent 36rem);"></div>
            <div class="wx-reveal wx-page-slide-up">
                <span class="wx-pill inline-flex rounded-full px-4 py-2 text-sm font-medium">{{ __('homepage.hero_badge') }}</span>
                <h1 class="mt-7 max-w-4xl text-5xl font-semibold tracking-[-0.045em] sm:text-6xl lg:text-7xl" style="color: var(--foreground);">{!! __('homepage.hero_title') !!}</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8" style="color: var(--muted-foreground);">{{ __('homepage.hero_copy') }}</p>
                <div class="mt-9 flex flex-wrap gap-3 wx-page-slide-up">
                    <a href="{{ route('signup') }}" class="wx-btn-primary px-6 py-3.5">{{ __('homepage.start_converting') }}</a>
                    <a href="{{ route('app.documentation') }}" class="wx-btn-secondary px-6 py-3.5">{{ __('homepage.view_documentation') }}</a>
                </div>
                <p class="mt-6 text-sm wx-page-fade" style="color: var(--muted-foreground);">{{ __('homepage.hero_note') }}</p>
            </div>

            <div class="relative wx-reveal wx-page-scale rounded-[24px] border border-white/[0.07] bg-[#0b0b0d] p-5 shadow-[0_24px_120px_rgba(255,255,255,.08)] before:pointer-events-none before:absolute before:inset-0 before:rounded-[24px] before:bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.16),transparent_18rem)] sm:p-6" style="animation-delay: 120ms">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white">{{ __('homepage.active_preset') }}</p>
                        <div class="mt-3 flex items-end gap-3"><span class="text-5xl font-semibold">2.3x</span><span class="pb-2 text-[#A3A3A3]">{{ __('homepage.amplify') }} -4 dB</span></div>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/[0.05] px-3 py-1 text-xs text-white">{{ __('homepage.live_preview') }}</span>
                </div>
                <div class="relative grid grid-cols-3 gap-2">
                    @foreach([['2.3x','-4 dB'], ['2.5x','-6 dB'], ['2.7x','-8 dB']] as [$speed, $amp])
                        <button class="wx-hover-lift rounded-[18px] border border-white/[0.08] bg-white/[0.035] p-4 text-left first:border-white/25 first:bg-white/[0.08]">
                            <span class="block text-lg font-semibold">{{ $speed }}</span>
                            <span class="text-sm text-[#A3A3A3]">{{ $amp }}</span>
                        </button>
                    @endforeach
                </div>
                <div class="relative mt-5 grid min-h-56 place-items-center rounded-[20px] border border-dashed border-white/10 bg-black/30 p-8 text-center">
                    <div>
                        <div class="mx-auto grid size-14 place-items-center rounded-2xl bg-white/[0.05] text-white"><svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M20 16v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3"/></svg></div>
                        <p class="mt-4 font-semibold">{{ __('homepage.drop_audio') }}</p>
                        <p class="mt-1 text-sm text-[#A3A3A3]">{{ __('homepage.browse_audio') }}</p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2 text-xs text-[#A3A3A3]">
                            @foreach(['OGG','MP3','WAV','M4A'] as $format)<span class="rounded-full border border-white/8 px-3 py-1">{{ $format }}</span>@endforeach
                        </div>
                    </div>
                </div>
                <button class="wx-btn-primary mt-5 w-full px-5 py-3.5">{{ __('homepage.convert_audio') }}</button>
            </div>
        </section>

        <section id="features" class="wx-feature-section relative overflow-hidden px-4 py-24 sm:px-6 lg:px-8">
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div class="wx-aurora absolute left-1/2 top-0 h-[34rem] w-[54rem] -translate-x-1/2 rounded-full blur-3xl"></div>
                <div class="wx-noise absolute inset-0 opacity-[.055]"></div>
            </div>
            <div class="mx-auto max-w-7xl">
                <div class="mx-auto max-w-3xl text-center wx-reveal">
                    <span class="wx-pill inline-flex rounded-full px-4 py-2 text-sm font-semibold">{{ __('homepage.features_badge') }}</span>
                    <h2 class="mt-6 text-4xl font-semibold tracking-[-0.045em] sm:text-6xl">{{ __('homepage.features_title') }}</h2>
                    <p class="mx-auto mt-5 max-w-2xl text-sm leading-7" style="color: var(--muted-foreground);">{{ __('homepage.features_copy') }}</p>
                </div>
                <div class="mt-12 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach([
                        [__('homepage.features.fast.title'), __('homepage.features.fast.copy'), 'M13 2 4 14h7l-1 8 10-13h-7V2Z', [18, 36, 54, 34, 72, 44, 62, 30]],
                        [__('homepage.features.formats.title'), __('homepage.features.formats.copy'), 'M9 18V5l12-2v13M6 21a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm12-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z', [30, 44, 38, 68, 50, 74, 40, 56]],
                        [__('homepage.features.secure.title'), __('homepage.features.secure.copy'), 'M12 3 20 7v5c0 5-3.4 8.3-8 9-4.6-.7-8-4-8-9V7l8-4Zm-3 9 2 2 4-5', [20, 26, 42, 64, 48, 36, 58, 30]],
                        [__('homepage.features.roblox.title'), __('homepage.features.roblox.copy'), 'M17.5 19H8a5 5 0 1 1 .9-9.92A7 7 0 0 1 22 12.5 4.5 4.5 0 0 1 17.5 19ZM12 12v5m-3-2 3-3 3 3', [42, 66, 54, 34, 72, 48, 60, 38]],
                        [__('homepage.features.optimization.title'), __('homepage.features.optimization.copy'), 'M21 12a9 9 0 0 1-15.4 6.36L3 16m0 5v-5h5M3 12A9 9 0 0 1 18.4 5.64L21 8m-5 0h5V3', [22, 58, 36, 70, 46, 62, 32, 50]],
                        [__('homepage.features.history.title'), __('homepage.features.history.copy'), 'M4 5c0 1.7 3.6 3 8 3s8-1.3 8-3-3.6-3-8-3-8 1.3-8 3Zm0 0v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3', [34, 48, 62, 40, 54, 76, 44, 60]],
                    ] as [$title, $copy, $path, $bars])
                        <article class="wx-feature-card wx-hover-lift group relative overflow-hidden rounded-[26px] p-[1px]">
                            <div class="wx-feature-card-inner relative h-full overflow-hidden rounded-[25px] p-6">
                                <div class="wx-feature-glow absolute -right-20 -top-20 h-44 w-44 rounded-full blur-3xl transition duration-500 group-hover:scale-125"></div>
                                <div class="relative flex items-start justify-between gap-4">
                                    <span class="wx-feature-icon grid size-13 place-items-center rounded-2xl">
                                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                                    </span>
                                    <div class="wx-mini-wave flex h-14 items-end gap-1">
                                        @foreach($bars as $height)
                                            <span style="height: {{ $height }}%" class="w-1.5 rounded-full"></span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="relative mt-16">
                                    <h3 class="text-xl font-semibold tracking-[-0.02em]">{{ $title }}</h3>
                                    <p class="mt-3 text-sm leading-6" style="color: var(--muted-foreground);">{{ $copy }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-16 grid items-center gap-8 lg:grid-cols-[.9fr_1.1fr]">
                    <div class="wx-reveal">
                        <h3 class="max-w-xl text-4xl font-semibold tracking-[-0.045em] sm:text-5xl">{{ __('homepage.features_title') }}</h3>
                        <p class="mt-5 max-w-xl text-sm leading-7" style="color: var(--muted-foreground);">{{ __('homepage.features_copy') }}</p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('app.converter') }}" class="wx-cta-gradient group inline-flex items-center gap-2 rounded-full px-5 py-3 text-sm font-semibold">{{ __('homepage.try_converter') }} <svg class="size-4 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m13 5 7 7-7 7"/></svg></a>
                            <a href="#pricing" class="wx-btn-secondary px-5 py-3 text-sm font-semibold">{{ __('homepage.explore_features') }}</a>
                        </div>
                    </div>
                    <div class="wx-showcase-card relative overflow-hidden rounded-[30px] p-6">
                        <div class="relative flex items-center justify-between">
                            <div><p class="text-xs font-semibold uppercase tracking-[0.24em]" style="color: var(--muted-foreground);">{{ __('homepage.processing') }}</p><h3 class="mt-2 text-2xl font-semibold">{{ __('homepage.ready_for_roblox') }}</h3></div>
                            <span class="wx-pill rounded-full px-3 py-1 text-xs font-semibold">{{ __('homepage.ready') }}</span>
                        </div>
                        <div class="mt-8 grid gap-5 md:grid-cols-[1fr_150px_1fr]">
                            <div class="wx-wave-panel rounded-3xl p-5"><p class="text-sm font-semibold">{{ __('homepage.original_audio') }}</p><div class="mt-5 flex h-28 items-center gap-1.5">@foreach([24,42,66,38,72,50,80,36,58,44,70,30,64,48,76,34] as $height)<span class="wx-spectrum-bar flex-1 rounded-full" style="height: {{ $height }}%; animation-delay: {{ $loop->index * 70 }}ms"></span>@endforeach</div></div>
                            <div class="grid place-items-center"><div class="wx-process-orb grid size-24 place-items-center rounded-full"><svg class="size-9 animate-spin" style="animation-duration: 5s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12a9 9 0 0 1-9 9"/><path d="M3 12a9 9 0 0 1 9-9"/><path d="M12 3v4"/><path d="M12 17v4"/></svg></div></div>
                            <div class="wx-wave-panel rounded-3xl p-5"><p class="text-sm font-semibold">{{ __('homepage.optimized_waveform') }}</p><svg class="mt-6 h-24 w-full" viewBox="0 0 320 110" fill="none"><path class="wx-wave-path" d="M0 70 C30 70 28 26 62 38 C94 50 90 92 126 74 C160 56 158 20 194 34 C228 48 224 84 260 66 C286 54 292 42 320 48" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M0 70 C30 70 28 26 62 38 C94 50 90 92 126 74 C160 56 158 20 194 34 C228 48 224 84 260 66 C286 54 292 42 320 48" stroke="currentColor" stroke-width="12" stroke-opacity=".08" stroke-linecap="round"/></svg></div>
                        </div>
                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                            @foreach([[\Illuminate\Support\Number::abbreviate($homepageStats['converted']), __('homepage.audio_converted')], [$homepageStats['average_duration'] > 0 ? number_format($homepageStats['average_duration'], 1).'s' : '< 10s', __('homepage.average_conversion')], [number_format($homepageStats['success'], 1).'%', __('homepage.success_rate')], [\Illuminate\Support\Number::abbreviate($homepageStats['downloads']), __('homepage.downloads')], [\Illuminate\Support\Number::abbreviate($homepageStats['credits_used']), __('homepage.credits_used')], [$homepageStats['queue_waiting'] > 0 ? $homepageStats['queue_waiting'].' '.__('homepage.waiting') : __('homepage.ready'), __('homepage.queue_status')]] as [$value, $label])
                                <div class="wx-metric-tile rounded-2xl p-4"><p class="text-2xl font-semibold tracking-[-0.03em]">{{ $value }}</p><p class="mt-1 text-xs" style="color: var(--muted-foreground);">{{ $label }}</p></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="hidden">
            <div class="grid items-center gap-10 lg:grid-cols-[1fr_560px]">
                <div class="wx-reveal">
                    <h2 class="max-w-xl text-4xl font-semibold tracking-[-0.04em] sm:text-5xl">Audio conversion engineered for maximum performance</h2>
                    <p class="mt-5 max-w-lg text-sm leading-6 text-[#A3A3A3]">Discover refined tools that streamline audio preparation, reduce manual work, and help your Roblox projects move faster.</p>
                    <a href="{{ route('signup') }}" class="mt-8 inline-flex rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-black transition hover:scale-[1.02]">Explore More ↗</a>
                </div>
                <div class="relative h-72 overflow-hidden rounded-[24px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_24px_100px_rgba(255,255,255,.06)]">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_40%_0%,rgba(255,255,255,.16),transparent_18rem)]"></div>
                    <div class="relative flex items-start justify-between">
                        <div><p class="text-3xl font-semibold">1,632</p><p class="text-xs text-[#A3A3A3]">Clicks</p></div>
                        <span class="h-1 w-12 rounded-full bg-white/70"></span>
                    </div>
                    <svg class="relative mt-12 h-28 w-full text-white/45" viewBox="0 0 420 140" fill="none">
                        <path d="M0 110 C65 110 58 30 126 44 C176 55 168 120 230 92 C286 66 274 38 330 52 C366 61 372 103 420 86" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span class="absolute left-[52%] top-[44%] rounded-full border border-white/10 bg-black/50 px-4 py-1 text-xs text-white/80">Chrome</span>
                </div>
            </div>
            <div class="mt-14 border-t border-white/[0.07] pt-7">
                <div class="grid gap-5 md:grid-cols-4">
                    @foreach([['AI Automation Systems','Streamline conversion processes with workflow automation.'], ['AI Development','Built around optimized processing for audio operations.'], ['Predictive Analytics','Forecast trends and success from your conversion history.'], ['Chatbots & Assistants','Enhance support with always-on help surfaces.']] as [$title, $copy])
                        <div>
                            <h3 class="text-sm font-semibold">{{ $title }}</h3>
                            <p class="mt-2 text-xs leading-5 text-[#A3A3A3]">{{ $copy }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="pricing" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-semibold tracking-[-0.04em] sm:text-5xl">{!! __('homepage.pricing_title') !!}</h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-[#A3A3A3]">{{ __('homepage.pricing_copy') }}</p>
            </div>
            <div class="mt-10 grid gap-4 md:grid-cols-4">
                @foreach($plans as $i => $plan)
                    <div class="wx-hover-lift relative overflow-hidden rounded-[22px] border border-white/[0.07] bg-[#0b0b0d] p-7 {{ $plan->slug === 'standard' ? 'bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.28),rgba(11,11,13,1)_58%)]' : '' }}">
                        @foreach([$plan->slug === 'standard' ? __('homepage.most_popular') : null, $plan->slug === 'premium' ? __('homepage.best_value') : null] as $badge)
                            @continue(! $badge)
                            <span class="absolute right-6 top-6 rounded-full bg-white px-3 py-1 text-xs font-medium text-black">{{ $badge }}</span>
                        @endforeach
                        <p class="text-lg text-white/80">{{ $plan->name }}</p>
                        <p class="mt-4 text-4xl font-semibold">{{ $plan->formattedPrice() }}</p>
                        <p class="mt-1 text-xs text-[#A3A3A3]">{{ __('homepage.credit_subscription') }}</p>
                        <div class="mt-8 space-y-4 text-sm text-white/80">
                            @foreach(($plan->features ?? []) as $item)
                                <p>- {{ $item }}</p>
                            @endforeach
                        </div>
                        <a href="{{ route('signup') }}" class="mt-9 block rounded-full border border-white/10 bg-white {{ $i === 1 ? 'text-black' : 'bg-white/[0.02] text-white' }} px-4 py-3 text-center text-sm font-semibold">{{ __('homepage.get_started') }}</a>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="reviews" class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8" x-data="{ filter: 'all' }">
            <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_18%_8%,rgba(168,85,247,.16),transparent_24rem),radial-gradient(circle_at_88%_36%,rgba(59,130,246,.12),transparent_22rem)]"></div>
            <div class="mb-10 grid gap-4 md:grid-cols-4">
                @foreach([
                    [\Illuminate\Support\Number::abbreviate($homepageStats['converted']), __('homepage.reviews_stats.converted')],
                    [\Illuminate\Support\Number::abbreviate($homepageStats['users']), __('homepage.reviews_stats.users')],
                    [number_format($homepageStats['rating'], 1), __('homepage.reviews_stats.rating')],
                    [number_format($homepageStats['success'], 1).'%', __('homepage.reviews_stats.success')],
                ] as [$value, $label])
                    <div class="rounded-[22px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_18px_70px_rgba(0,0,0,.25)]">
                        <p class="text-3xl font-semibold text-white">{{ $value }}</p>
                        <p class="mt-2 text-sm text-[#A3A3A3]">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mb-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.24em] text-white/45">{{ __('homepage.social_proof') }}</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-5xl">{{ __('homepage.reviews_title') }}</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-6 text-[#A3A3A3]">{{ __('homepage.reviews_copy') }}</p>
                </div>
                <div class="w-full max-w-md rounded-[24px] border border-white/[0.08] bg-[#0b0b0d]/90 p-5 shadow-[0_22px_80px_rgba(0,0,0,.35)] backdrop-blur">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <x-community.rating-stars :rating="$reviewSummary['average']" size="20" showNumber />
                        </div>
                        <p class="pb-1 text-sm text-[#A3A3A3]">{{ __('homepage.based_on_reviews', ['count' => number_format($reviewSummary['count'])]) }}</p>
                    </div>
                    <div class="mt-5 space-y-2">
                        <?php for ($starValue = 5; $starValue >= 1; $starValue--): ?>
                            <?php $percent = $reviewSummary['distribution'][$starValue]['percent']; ?>
                            <div class="grid grid-cols-[52px_1fr_34px] items-center gap-3 text-xs text-[#A3A3A3]">
                                <span>{{ $starValue }} {{ __('homepage.star_word') }}</span>
                                <div class="h-2 overflow-hidden rounded-full bg-white/10">
                                    <div class="h-full rounded-full bg-white" style="width: {{ $percent }}%"></div>
                                </div>
                                <span>{{ $percent }}%</span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex gap-2 overflow-x-auto pb-2 [scrollbar-width:none]">
                @foreach(__('homepage.filters') as $key => $label)
                    <button type="button" @click="filter = '{{ $key }}'" :class="filter === '{{ $key }}' ? 'bg-white text-black shadow-[0_0_28px_rgba(255,255,255,.16)]' : 'bg-white/[0.04] text-[#A3A3A3] hover:border-white/20 hover:text-white'" class="shrink-0 rounded-full border border-white/10 px-4 py-2 text-xs font-semibold transition">
                        {{ $label }} <span class="opacity-60">({{ $reviewFilterCounts[$key] ?? 0 }})</span>
                    </button>
                @endforeach
            </div>

            <div class="flex snap-x gap-5 overflow-x-auto pb-4 lg:grid lg:grid-cols-3 lg:overflow-visible">
                @forelse($homepageReviews as $review)
                        <article
                            x-show="filter === 'all' || filter === '{{ $review->rating }}' || '{{ $review->homepage_badge_slugs }}'.includes(filter)"
                            x-transition
                            class="min-w-[86%] snap-start rounded-[28px] border border-white/[0.08] bg-[#0b0b0d]/92 p-6 shadow-[0_24px_90px_rgba(0,0,0,.38)] backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_30px_110px_rgba(168,85,247,.15)] sm:min-w-[420px] lg:min-w-0"
                        >
                            <div class="flex items-start gap-4">
                                <div class="group/avatar relative shrink-0">
                                    <div class="grid size-12 place-items-center overflow-hidden rounded-full border border-white/10 bg-gradient-to-br {{ $review->homepage_gradient }} text-sm font-semibold text-white shadow-[0_12px_35px_rgba(0,0,0,.25)]">
                                        @if($review->homepage_avatar)
                                            <img src="{{ str_starts_with($review->homepage_avatar, 'http') ? $review->homepage_avatar : asset('storage/'.$review->homepage_avatar) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="pointer-events-none absolute left-0 top-14 z-30 w-72 origin-top-left scale-95 rounded-2xl border border-white/10 bg-[#0b0b0d]/95 p-4 opacity-0 shadow-[0_28px_90px_rgba(0,0,0,.45)] backdrop-blur-xl transition duration-200 group-hover/avatar:scale-100 group-hover/avatar:opacity-100">
                                        <div class="flex items-center gap-3">
                                            <div class="grid size-12 place-items-center overflow-hidden rounded-full border border-white/10 bg-gradient-to-br {{ $review->homepage_gradient }} text-sm font-semibold text-white">
                                                @if($review->homepage_avatar)
                                                    <img src="{{ str_starts_with($review->homepage_avatar, 'http') ? $review->homepage_avatar : asset('storage/'.$review->homepage_avatar) }}" alt="" class="h-full w-full object-cover">
                                                @else
                                                    {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-white">{{ $review->user?->name ?? __('homepage.creator') }}</p>
                                                <div class="mt-1 flex flex-wrap gap-1.5">
                                                    @foreach(collect([$review->homepage_membership_badge, $review->homepage_verified_badge])->filter() as $badge)
                                                        <x-community.badge :badge="$badge" />
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 grid grid-cols-2 gap-3 text-xs text-[#A3A3A3]">
                                            <p>{{ __('homepage.member_since') }} <span class="mt-1 block text-white">{{ $review->homepage_user_since }}</span></p>
                                            <p>{{ __('homepage.current_plan') }} <span class="mt-1 block text-white">{{ $review->homepage_user_plan }}</span></p>
                                            <p>{{ __('homepage.downloads') }} <span class="mt-1 block text-white">{{ $review->homepage_user_downloads }}</span></p>
                                            <p>{{ __('homepage.conversions') }} <span class="mt-1 block text-white">{{ $review->homepage_user_conversions }}</span></p>
                                            <p>{{ __('homepage.reviews') }} <span class="mt-1 block text-white">{{ $review->homepage_user_reviews }}</span></p>
                                            <p>{{ __('homepage.helpful') }} <span class="mt-1 block text-white">{{ $review->homepage_user_helpful_received }}</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="font-semibold text-white">{{ $review->user?->name ?? __('homepage.creator') }}</p>
                                        @foreach(collect([$review->homepage_membership_badge, $review->homepage_verified_badge])->filter() as $badge)
                                            <x-community.badge :badge="$badge" />
                                        @endforeach
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @if($review->is_featured)
                                            <span class="rounded-full border border-white/10 bg-white px-3 py-1 text-xs font-semibold text-black">{{ __('homepage.editors_choice') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <x-community.rating-stars :rating="$review->rating" size="18" showTooltip class="mt-5" />
                            <h3 class="mt-3 text-xl font-semibold text-white">{{ $review->title }}</h3>
                            <p class="mt-3 leading-7 text-[#D4D4D4]">{{ Str::limit($review->content, 170) }}</p>
                            <div class="mt-5 flex flex-wrap gap-2 border-t border-white/[0.06] pt-4 text-sm text-[#A3A3A3]">
                                <a href="{{ route('signin') }}" class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 transition hover:border-white/20 hover:text-white"><x-community.icon name="thumbs-up" class="size-4" /> {{ $review->helpful_count }} {{ __('homepage.helpful') }}</a>
                                <a href="{{ route('signin') }}" class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 transition hover:border-white/20 hover:text-white"><x-community.icon name="reply" class="size-4" /> {{ __('homepage.reply') }}</a>
                                <a href="{{ route('signin') }}" class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 transition hover:border-white/20 hover:text-white"><x-community.icon name="flag" class="size-4" /> {{ __('homepage.report') }}</a>
                                <a href="{{ route('signin') }}" class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 transition hover:border-white/20 hover:text-white"><x-community.icon name="eye" class="size-4" /> {{ $review->all_comments_count }} {{ __('homepage.replies') }}</a>
                                <span class="inline-flex items-center gap-1.5 px-1 py-2"><x-community.icon name="clock" class="size-4" /> {{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </article>
                @empty
                    <div class="rounded-[28px] border border-white/[0.08] bg-[#0b0b0d] p-10 text-center lg:col-span-3">
                        <p class="text-2xl font-semibold text-white">{{ __('homepage.no_reviews') }}</p>
                        <p class="mt-2 text-[#A3A3A3]">{{ __('homepage.no_reviews_copy') }}</p>
                        <a href="{{ route('signin') }}" class="mt-6 inline-flex wx-btn-primary px-5 py-3">{{ __('homepage.write_review') }}</a>
                    </div>
                @endforelse
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('signin') }}" class="wx-btn-primary inline-flex px-6 py-3.5">{{ __('homepage.write_review') }}</a>
                </div>
        </section>

        <section id="faq" class="mx-auto max-w-5xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-semibold tracking-[-0.04em] text-white sm:text-5xl">{{ __('homepage.faq_title') }}</h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-[#A3A3A3]">{{ __('homepage.faq_copy') }}</p>
            </div>
            <div class="mt-10 grid gap-3">
                @foreach(__('homepage.faq') as $item)
                    <div class="rounded-[22px] border border-white/[0.07] bg-[#0b0b0d] p-6">
                        <p class="font-semibold text-white">{{ $item['question'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-[#A3A3A3]">{{ $item['answer'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="wx-card wx-glow bg-gradient-to-br from-[#FFFFFF]/14 to-white/[0.03] p-8 text-center sm:p-12">
                <h2 class="text-3xl font-semibold sm:text-5xl">{{ __('homepage.cta_title') }}</h2>
                <p class="mt-4 text-[#A3A3A3]">{{ __('homepage.cta_copy') }}</p>
                <a href="{{ route('signup') }}" class="wx-btn-primary mt-8 inline-flex px-6 py-3.5">{{ __('homepage.get_started') }}</a>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/8 px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-5">
            <div class="md:col-span-2"><p class="font-semibold">NPNHCREATIVE</p><p class="mt-2 max-w-sm text-sm text-[#A3A3A3]">{{ __('homepage.footer_copy') }}</p></div>
            @foreach(__('homepage.footer_groups') as $title => $links)
                <div>
                    <p class="font-medium">{{ $title }}</p>
                    <div class="mt-3 grid gap-2 text-sm text-[#A3A3A3]">
                        @foreach($links as $link)
                            <a href="#" class="hover:text-white">{{ $link }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <p class="mx-auto mt-10 max-w-7xl text-sm text-[#A3A3A3]">{{ __('homepage.copyright') }}</p>
    </footer>
    </div>
</body>
</html>
