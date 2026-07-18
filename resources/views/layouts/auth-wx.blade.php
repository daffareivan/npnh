<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('auth.title') }} | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script>
        (() => {
            const savedTheme = localStorage.getItem('npnhcreative_theme') || @json($currentTheme ?? 'system');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const effectiveTheme = savedTheme === 'system' ? systemTheme : savedTheme;
            document.documentElement.dataset.theme = savedTheme;
            document.documentElement.classList.toggle('dark', effectiveTheme === 'dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes wx-wave {
            0%, 100% { transform: scaleY(.58); opacity: .55; }
            50% { transform: scaleY(1); opacity: 1; }
        }

        .wx-wave-bar {
            animation: wx-wave 2s ease-in-out infinite;
            transform-origin: bottom;
        }
    </style>
</head>
<body class="wx-shell wx-auth min-h-screen antialiased" x-data="{
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
    @php
        $authStats = cache()->remember('auth.hero.stats', 60, function () {
            $hasAudioFiles = \Illuminate\Support\Facades\Schema::hasTable('audio_files');
            $hasUsers = \Illuminate\Support\Facades\Schema::hasTable('users');
            $hasCreditTransactions = \Illuminate\Support\Facades\Schema::hasTable('credit_transactions');

            return [
                'converted' => $hasAudioFiles
                    ? \App\Models\AudioFile::query()->where('status', \App\Enums\ConversionStatus::Finished->value)->count()
                    : 0,
                'users' => $hasUsers ? \App\Models\User::query()->count() : 0,
                'credits' => $hasCreditTransactions
                    ? abs((int) \App\Models\CreditTransaction::query()->where('amount', '<', 0)->sum('amount'))
                    : 0,
            ];
        });
    @endphp
    <div class="wx-auth-controls fixed right-5 top-5 z-50 hidden items-center gap-2 rounded-full p-1.5 md:flex">
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
    </div>

    <main class="grid min-h-screen md:grid-cols-[1fr_1fr] lg:grid-cols-[1fr_520px]">
        <section class="relative hidden overflow-hidden p-6 md:flex md:flex-col md:justify-between lg:p-10">
            <div class="wx-decor-glow pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(168,85,247,.24),transparent_28rem),radial-gradient(circle_at_76%_68%,rgba(59,130,246,.20),transparent_26rem),radial-gradient(circle_at_42%_92%,rgba(16,185,129,.10),transparent_24rem)]"></div>
            <div class="wx-decor-glow pointer-events-none absolute -left-32 top-24 h-72 w-72 rounded-full bg-violet-500/10 blur-3xl"></div>
            <div class="wx-decor-glow pointer-events-none absolute -right-28 bottom-20 h-80 w-80 rounded-full bg-blue-500/10 blur-3xl"></div>
            <div class="wx-decor-glow pointer-events-none absolute inset-0 opacity-[.07] [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:22px_22px]"></div>
            <div class="relative z-20 mb-10 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid size-11 place-items-center overflow-hidden rounded-full">
                    <img src="{{ asset('images/logo.png') }}" alt="NPNHCREATIVE" class="size-full object-contain">
                </span>
                <span class="font-semibold">NPNHCREATIVE</span>
            </a>
                <div class="hidden items-center gap-2">
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
                </div>
            </div>
            <div class="relative z-10 max-w-2xl wx-reveal">
                <p class="mb-5 inline-flex rounded-full border border-white/10 bg-white/[0.05] px-4 py-2 text-sm text-white">{{ __('auth.tagline') }}</p>
                <h1 class="text-4xl font-semibold tracking-[-0.05em] lg:text-6xl">{{ __('auth.hero_title') }}</h1>
                <p class="mt-6 text-lg leading-8 text-[#A3A3A3]">{{ __('auth.hero_copy') }}</p>
                <div class="mt-8 grid gap-3 text-sm text-[#E5E5E5] sm:grid-cols-2">
                    @foreach(__('auth.benefits') as $benefit)
                        <p class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3"><svg class="size-4 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m20 6-11 11-5-5"/></svg><span>{{ $benefit }}</span></p>
                    @endforeach
                </div>
                <div class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-3">
                    @foreach([
                        [\Illuminate\Support\Number::abbreviate($authStats['converted']), __('auth.converted')],
                        [\Illuminate\Support\Number::abbreviate($authStats['users']), __('auth.creators')],
                        [\Illuminate\Support\Number::abbreviate($authStats['credits']), __('auth.credits_used')],
                    ] as [$value, $label])
                        <div class="rounded-3xl border border-white/10 bg-white/[0.055] p-5 shadow-[0_18px_60px_rgba(0,0,0,.22)] backdrop-blur">
                            <p class="text-3xl font-semibold tracking-[-0.05em] text-white lg:text-4xl">{{ $value }}</p>
                            <p class="mt-1 text-xs text-[#A3A3A3]">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="relative z-10 mb-6 grid max-w-2xl gap-4">
                <div class="rounded-[28px] border border-white/10 bg-white/[0.05] p-5 shadow-[0_24px_90px_rgba(0,0,0,.28)] backdrop-blur">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-white">{{ __('auth.audio_pipeline') }}</p>
                        <span class="inline-flex items-center gap-1 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs text-emerald-200">
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                            {{ __('auth.ready') }}
                        </span>
                    </div>
                    <div class="mt-5 flex items-end gap-1.5">
                        @foreach([28,44,62,38,76,54,34,68,42,58,30,72,48,64,36,52] as $height)
                            <span class="wx-wave-bar w-full rounded-full bg-white/70 transition duration-500 hover:bg-white" style="height: {{ $height }}px; animation-delay: {{ $loop->index * 90 }}ms"></span>
                        @endforeach
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-2 text-center text-xs text-[#A3A3A3] lg:grid-cols-5">
                        @foreach([
                            [__('auth.upload'), 'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4|M17 8l-5-5-5 5|M12 3v12'],
                            ['2.3x', 'M4 18V6|M8 18V10|M12 18V4|M16 18v-8|M20 18V7'],
                            [__('auth.convert'), 'M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16|M3 21v-5h5|M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8|M16 8h5V3'],
                            [__('auth.download'), 'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4|M7 10l5 5 5-5|M12 15V3'],
                            [__('auth.roblox'), 'M12 2 3 7v10l9 5 9-5V7l-9-5Z|M12 8v8|M8 10l4 2 4-2'],
                        ] as [$step, $icon])
                            <span class="grid place-items-center gap-1 rounded-2xl border border-white/10 bg-black/20 px-2 py-2">
                                <svg class="size-4 text-white/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    @foreach(explode('|', $icon) as $path)
                                        <path d="{{ $path }}"/>
                                    @endforeach
                                </svg>
                                {{ $step }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 lg:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.045] p-4">
                        <p class="text-xs text-[#A3A3A3]">{{ __('auth.active_preset') }}</p>
                        <p class="mt-2 text-xl font-semibold text-white lg:text-2xl">2.3x</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.045] p-4">
                        <p class="text-xs text-[#A3A3A3]">{{ __('auth.status') }}</p>
                        <p class="mt-2 flex items-center gap-2 text-sm font-semibold text-emerald-200">
                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                            {{ __('auth.uploaded_successfully') }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.045] p-4">
                        <p class="text-xs text-[#A3A3A3]">{{ __('auth.credits_remaining') }}</p>
                        <p class="mt-2 text-xl font-semibold text-white lg:text-2xl">250</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center px-4 py-10 sm:px-6">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center justify-between gap-3 md:hidden">
                    <a href="{{ route('home') }}" class="flex items-center gap-3"><span class="grid size-10 place-items-center overflow-hidden rounded-full"><img src="{{ asset('images/logo.png') }}" alt="NPNHCREATIVE" class="size-full object-contain"></span><span class="font-semibold">NPNHCREATIVE</span></a>
                    <div class="flex items-center gap-2">
                        @if($allowThemeSwitch ?? true)
                            <form method="POST" action="{{ route('preferences.theme') }}">
                                @csrf
                                <input type="hidden" name="theme" x-bind:value="theme === 'dark' ? 'light' : 'dark'">
                                <button type="submit" @click="setTheme(theme === 'dark' ? 'light' : 'dark')" class="wx-icon-button grid size-10 place-items-center rounded-full" aria-label="{{ __('ui.theme') }}">
                                    <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M2 12h2"/><path d="M20 12h2"/></svg>
                                </button>
                            </form>
                        @endif
                        @if($allowLanguageSwitch ?? true)
                            <form method="POST" action="{{ route('preferences.locale') }}">
                                @csrf
                                <input type="hidden" name="locale" value="{{ ($currentLocale ?? 'en') === 'en' ? 'id' : 'en' }}">
                                <button type="submit" class="wx-icon-button grid size-10 place-items-center rounded-full text-xs font-semibold" aria-label="{{ __('ui.language') }}">{{ strtoupper(($currentLocale ?? 'en') === 'en' ? 'ID' : 'EN') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="wx-card p-5 shadow-[0_26px_100px_rgba(0,0,0,.38)] sm:p-6">
                    @if(session('status'))
                        <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
                    @endif
                    @yield('content')
                </div>
                <footer class="mt-6 flex justify-center gap-5 text-xs text-[#A3A3A3]">
                    <a href="{{ route('privacy') }}" class="hover:text-white">{{ __('auth.privacy') }}</a>
                    <a href="{{ route('terms') }}" class="hover:text-white">{{ __('auth.terms') }}</a>
                    <a href="{{ route('home') }}#reviews" class="hover:text-white">{{ __('auth.reviews') }}</a>
                </footer>
            </div>
        </section>
    </main>
</body>
</html>

