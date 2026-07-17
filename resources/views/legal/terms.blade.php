<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms of Service | NPNHCREATIVE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="wx-shell min-h-screen antialiased">
    <main class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
        <a href="{{ route('home') }}" class="text-sm text-[#A3A3A3] hover:text-white">NPNHCREATIVE</a>
        <section class="wx-card mt-8 p-6 sm:p-8">
            <h1 class="text-4xl font-semibold tracking-tight">Terms of Service</h1>
            <p class="mt-4 text-sm leading-7 text-[#A3A3A3]">By using NPNHCREATIVE, you agree to use the service only for files and creator workflows you are authorized to manage.</p>
            <div class="mt-8 space-y-6 text-sm leading-7 text-[#E5E5E5]">
                <p>The service provides audio conversion and integration helpers. Roblox upload automation is only offered when supported by official Roblox APIs. If official API support is unavailable, users must manually upload assets through Roblox Creator Hub.</p>
                <p>Users are responsible for complying with Roblox community standards, copyright rules, and platform moderation requirements for uploaded assets.</p>
                <p>NPNHCREATIVE does not request Roblox session cookies and does not use scraping, internal APIs, or reverse-engineered endpoints.</p>
            </div>
        </section>
    </main>
</body>
</html>
