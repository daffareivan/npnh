@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-white">Roblox Integration</p>
                <h1 class="mt-2 text-4xl font-semibold tracking-tight">Roblox Integration</h1>
                <p class="mt-2 text-[#A3A3A3]">Connect your Roblox account to simplify your creator workflow.</p>
            </div>
            <a href="{{ route('app.integrations') }}" class="wx-btn-secondary px-5 py-3 text-center">All Integrations</a>
        </div>

        @if(session('status'))
            <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
        @endif

        <div class="wx-card p-6 sm:p-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold">Roblox Account</h2>
                    @if($account)
                        <div class="mt-3 flex items-center gap-2">
                            <x-wx.badge tone="success">Connected</x-wx.badge>
                            <span class="text-sm text-[#A3A3A3]">Connected since {{ $account->created_at->format('M d, Y') }}</span>
                        </div>
                    @else
                        <div class="mt-3 flex items-center gap-2">
                            <x-wx.badge>Not Connected</x-wx.badge>
                            <span class="text-sm text-[#A3A3A3]">Official authentication required.</span>
                        </div>
                    @endif
                </div>

                @if($account)
                    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-3">
                        <a href="https://www.roblox.com/users/{{ $account->roblox_user_id }}/profile" target="_blank" rel="noopener" class="wx-btn-secondary w-full px-5 py-3 text-center sm:w-auto">View Profile</a>
                        <form method="POST" action="{{ route('roblox.switch') }}">
                            @csrf
                            <button class="wx-btn-primary w-full px-5 py-3 sm:w-auto">Switch Account</button>
                        </form>
                        <form method="POST" action="{{ route('roblox.disconnect') }}">
                            @csrf
                            @method('DELETE')
                            <button class="w-full rounded-full border border-rose-400/20 bg-rose-400/10 px-5 py-3 text-sm font-semibold text-rose-200 transition hover:bg-rose-400/15 sm:w-auto">Disconnect</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('roblox.connect') }}" class="wx-btn-primary block px-5 py-3 text-center sm:inline-block sm:w-auto">Connect Roblox</a>
                @endif
            </div>

            @if($account)
                <div class="mt-8 grid gap-4 sm:grid-cols-[auto_1fr]">
                    <div class="grid size-24 place-items-center overflow-hidden rounded-[24px] border border-white/10 bg-white/[0.05] text-3xl font-semibold">
                        @if($account->avatar_url)
                            <img src="{{ $account->avatar_url }}" class="h-full w-full object-cover" alt="Roblox avatar">
                        @else
                            {{ strtoupper(substr($account->display_name ?: $account->username ?: 'R', 0, 1)) }}
                        @endif
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[20px] border border-white/[0.06] bg-black/20 p-4"><p class="text-sm text-[#A3A3A3]">Display Name</p><p class="mt-1 font-semibold">{{ $account->display_name ?: '-' }}</p></div>
                        <div class="rounded-[20px] border border-white/[0.06] bg-black/20 p-4"><p class="text-sm text-[#A3A3A3]">Username</p><p class="mt-1 font-semibold">{{ $account->username ?: '-' }}</p></div>
                        <div class="rounded-[20px] border border-white/[0.06] bg-black/20 p-4"><p class="text-sm text-[#A3A3A3]">User ID</p><p class="mt-1 font-semibold">{{ $account->roblox_user_id }}</p></div>
                        <div class="rounded-[20px] border border-white/[0.06] bg-black/20 p-4"><p class="text-sm text-[#A3A3A3]">Provider</p><p class="mt-1 font-semibold">Official OAuth</p></div>
                    </div>
                </div>
            @else
                <div class="mt-8 rounded-[22px] border border-white/[0.06] bg-black/20 p-5">
                    <p class="text-[#A3A3A3]">Connect your Roblox account using official authentication. NPNHCREATIVE never asks for .ROBLOSECURITY cookies and never uses scraping or internal endpoints.</p>
                </div>
            @endif
        </div>

        <div class="mt-5 wx-card-solid p-6">
            <h2 class="text-xl font-semibold">Creator Hub Workflow</h2>
            <p class="mt-2 text-sm leading-6 text-[#A3A3A3]">NPNHCREATIVE can upload converted audio through the official Roblox Open Cloud Assets API when this OAuth app has the <span class="text-white">asset:read</span> and <span class="text-white">asset:write</span> scopes. If you connected before enabling those scopes, disconnect and connect Roblox again.</p>
            <a href="{{ $creatorHubUrl }}" target="_blank" rel="noopener" class="wx-btn-secondary mt-5 inline-flex px-5 py-3">Open Creator Hub</a>
        </div>
    </section>
@endsection
