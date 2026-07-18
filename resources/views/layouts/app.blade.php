<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | NPNHCREATIVE</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const serverTheme = @json($currentTheme ?? 'system');
                    const savedTheme = localStorage.getItem('npnhcreative_theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    this.theme = savedTheme || serverTheme || 'system';
                    this.effectiveTheme = this.theme === 'system' ? systemTheme : this.theme;
                    this.updateTheme();
                },
                theme: 'system',
                effectiveTheme: 'dark',
                toggle() {
                    this.theme = this.effectiveTheme === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('npnhcreative_theme', this.theme);
                    this.updateTheme();
                },
                setTheme(theme) {
                    this.theme = theme;
                    localStorage.setItem('npnhcreative_theme', theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.effectiveTheme = this.theme === 'system' ? systemTheme : this.theme;
                    const html = document.documentElement;
                    const body = document.body;
                    html.dataset.theme = this.theme;
                    if (this.effectiveTheme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'wx-admin-bg');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark');
                        body.classList.add('wx-admin-bg');
                    }
                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size: desktop (>=1280) expanded, tablet/laptop (>=768) mini rail, mobile hidden
                isExpanded: window.innerWidth >= 1280,
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover-to-preview once the sidebar is at least the visible mini rail (>=768) and collapsed
                    if (window.innerWidth >= 768 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('npnhcreative_theme') || @json($currentTheme ?? 'system');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme === 'system' ? systemTheme : savedTheme;
            document.documentElement.dataset.theme = savedTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.addEventListener('DOMContentLoaded', () => document.body.classList.add('dark', 'wx-admin-bg'));
            } else {
                document.documentElement.classList.remove('dark');
                document.addEventListener('DOMContentLoaded', () => {
                    document.body.classList.remove('dark');
                    document.body.classList.add('wx-admin-bg');
                });
            }
        })();
    </script>
    
</head>

<body
    class="wx-admin-bg"
    x-data="{ 'loaded': true}"
    x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
    const checkMobile = () => {
        if (window.innerWidth < 768) {
            $store.sidebar.setMobileOpen(false);
            $store.sidebar.isExpanded = false;
        } else if (window.innerWidth < 1280) {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = false;
        } else {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = true;
        }
    };
    window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader/>
    {{-- preloader end --}}

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'md:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'md:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            <!-- app header start -->
            @include('layouts.app-header')
            <!-- app header end -->
            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-8">
                @yield('content')
            </div>
        </div>

    </div>

</body>

@stack('scripts')

</html>
