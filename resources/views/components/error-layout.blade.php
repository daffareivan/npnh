@props(['code' => 500, 'title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="{{ $title }} — {{ __('errors.meta_description') }}">
    <title>{{ $title }} | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('npnhcreative_theme') || 'system';
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme === 'system' ? systemTheme : savedTheme;
            document.documentElement.dataset.theme = savedTheme;
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="wx-shell wx-marketing min-h-screen antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="wx-decor-glow absolute left-1/2 top-[-18rem] h-[34rem] w-[34rem] -translate-x-1/2 rounded-full blur-3xl" style="background: color-mix(in srgb, var(--primary) 18%, transparent);"></div>
        <div class="wx-decor-glow absolute right-[-8rem] bottom-[-6rem] h-80 w-80 rounded-full blur-3xl" style="background: color-mix(in srgb, var(--foreground) 8%, transparent);"></div>
    </div>

    <main class="relative flex min-h-screen flex-col items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="mb-8 text-sm text-muted-foreground transition hover:text-foreground" aria-label="{{ config('app.name') }} — {{ __('pages.home') }}">
            {{ config('app.name') }}
        </a>

        {{ $slot }}
    </main>
</body>
</html>
