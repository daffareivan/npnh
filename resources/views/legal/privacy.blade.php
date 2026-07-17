<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy | NPNHCREATIVE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="wx-shell min-h-screen antialiased">
    <main class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
        <a href="{{ route('home') }}" class="text-sm text-[#A3A3A3] hover:text-white">NPNHCREATIVE</a>
        <section class="wx-card mt-8 p-6 sm:p-8">
            <h1 class="text-4xl font-semibold tracking-tight">Privacy Policy</h1>
            <p class="mt-4 text-sm leading-7 text-[#A3A3A3]">NPNHCREATIVE collects only the information needed to provide account access, audio conversion, download history, and official third-party integrations such as Google and Roblox OAuth.</p>
            <div class="mt-8 space-y-6 text-sm leading-7 text-[#E5E5E5]">
                <p>OAuth access tokens are stored securely and are never shown in the dashboard, sent to the frontend, or logged. Roblox integration does not use .ROBLOSECURITY cookies, scraping, or private endpoints.</p>
                <p>Uploaded files are used for conversion workflows and may be stored temporarily based on application settings. Users can delete conversion history and disconnect connected accounts from their profile or integrations page.</p>
                <p>For questions about privacy or data removal, contact the application owner.</p>
            </div>
        </section>
    </main>
</body>
</html>
