@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-7">
            <p class="text-sm font-medium text-white">Integrations</p>
            <h1 class="mt-2 text-4xl font-semibold tracking-tight">Connected creator tools</h1>
            <p class="mt-2 text-[#A3A3A3]">Connect official accounts to simplify your NPNHCREATIVE workflow.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('app.profile') }}" class="wx-card-solid wx-hover-lift p-6">
                <div class="mb-5 grid size-11 place-items-center rounded-2xl border border-white/10 bg-white/[0.05]">G</div>
                <h2 class="text-xl font-semibold">Google</h2>
                <p class="mt-2 text-sm text-[#A3A3A3]">One-click authentication for your account.</p>
                <x-wx.badge class="mt-5" :tone="auth()->user()->provider === 'google' ? 'success' : 'default'">{{ auth()->user()->provider === 'google' ? 'Connected' : 'Not Connected' }}</x-wx.badge>
            </a>

            <a href="{{ route('app.integrations.roblox') }}" class="wx-card-solid wx-hover-lift p-6">
                <div class="mb-5 grid size-11 place-items-center rounded-2xl border border-white/10 bg-white/[0.05]">R</div>
                <h2 class="text-xl font-semibold">Roblox</h2>
                <p class="mt-2 text-sm text-[#A3A3A3]">Connect your Roblox account for creator workflows.</p>
                <x-wx.badge class="mt-5" :tone="auth()->user()->robloxAccount ? 'success' : 'default'">{{ auth()->user()->robloxAccount ? 'Connected' : 'Not Connected' }}</x-wx.badge>
            </a>

            <div class="wx-card-solid p-6 opacity-70">
                <div class="mb-5 grid size-11 place-items-center rounded-2xl border border-white/10 bg-white/[0.05]">+</div>
                <h2 class="text-xl font-semibold">Coming Soon</h2>
                <p class="mt-2 text-sm text-[#A3A3A3]">More creator integrations are planned.</p>
            </div>
        </div>
    </section>
@endsection
