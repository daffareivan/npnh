@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <x-wx.section-card class="mb-6 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.08),transparent_18rem)]">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-white">Welcome Back</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">Ready to convert your next Roblox audio?</h1>
                    <p class="mt-2 text-[#A3A3A3]">Fast Audio Converter for Roblox</p>
                </div>
                <a href="{{ route('app.converter') }}" class="wx-btn-primary px-5 py-3 text-center">Upload Audio</a>
            </div>
        </x-wx.section-card>

        @php
            $planTotal = $planCredits ?: max($creditBalance, 1);
            $creditProgress = min(100, (int) round(($creditBalance / max($planTotal, 1)) * 100));
        @endphp

        <div class="mb-6 grid gap-4 md:grid-cols-3">
            <x-wx.section-card>
                <p class="text-sm text-[#A3A3A3]">Current Plan</p>
                <div class="mt-3 flex items-center justify-between gap-3">
                    <p class="text-2xl font-semibold text-white">{{ $currentPlan->name }}</p>
                    @if($membershipBadge)
                        <x-community.badge :badge="$membershipBadge" />
                    @else
                        <span class="rounded-full border border-white/10 bg-white px-3 py-1 text-xs font-semibold text-black">Active</span>
                    @endif
                </div>
            </x-wx.section-card>
            <x-wx.section-card>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-[#A3A3A3]">Credits</p>
                    <p class="text-sm text-white">{{ $creditBalance }} / {{ $planCredits ?? 'Unlimited' }}</p>
                </div>
                <p class="mt-3 text-2xl font-semibold text-white">{{ $creditBalance }} Remaining</p>
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/10">
                    <div class="h-full rounded-full bg-white" style="width: {{ $creditProgress }}%"></div>
                </div>
            </x-wx.section-card>
            <x-wx.section-card>
                <p class="text-sm text-[#A3A3A3]">Upgrade Plan</p>
                <p class="mt-3 text-2xl font-semibold text-white">Need more credits?</p>
                <a href="{{ route('app.pricing') }}" class="mt-4 inline-flex wx-btn-secondary px-4 py-2">View Plans</a>
            </x-wx.section-card>
        </div>

        <div class="mb-6 grid gap-4 md:grid-cols-4">
            <x-wx.stat-card label="Total Conversion" :value="$stats['total']" />
            <x-wx.stat-card label="Today's Conversion" :value="$stats['today']" />
            <x-wx.stat-card label="Success Rate" value="99.9%" />
            <x-wx.stat-card label="Credits Balance" :value="$creditBalance.' Credits'" />
        </div>

        <x-wx.section-card class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-[#A3A3A3]">Roblox</p>
                    <h2 class="mt-1 text-2xl font-semibold">{{ $robloxAccount ? 'Connected' : 'Not Connected' }}</h2>
                    @if($robloxAccount)
                        <p class="mt-2 text-sm text-[#A3A3A3]">{{ $robloxAccount->display_name ?: $robloxAccount->username }} {{ $robloxAccount->username ? '(@'.$robloxAccount->username.')' : '' }}</p>
                    @else
                        <p class="mt-2 text-sm text-[#A3A3A3]">Connect Roblox to prepare creator workflows.</p>
                    @endif
                </div>
                <a href="{{ route('app.integrations.roblox') }}" class="{{ $robloxAccount ? 'wx-btn-secondary' : 'wx-btn-primary' }} px-5 py-3 text-center">{{ $robloxAccount ? 'View Profile' : 'Connect Roblox' }}</a>
            </div>
        </x-wx.section-card>

        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1fr_420px] lg:items-center">
            <div>
                <p class="mb-4 inline-flex rounded-full border border-white/10 bg-white/[0.04] px-4 py-2 text-sm text-white/80">Roblox-ready OGG conversion</p>
                <h1 class="max-w-3xl text-5xl font-semibold tracking-tight text-white sm:text-6xl">Convert Roblox Audio Instantly</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Upload your MP3, WAV, M4A or OGG. Choose preset. Convert. Download.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#converter" class="wx-btn-primary px-5 py-3">Upload Audio</a>
                    <a href="{{ route('app.documentation') }}" class="wx-btn-secondary px-5 py-3">Documentation</a>
                </div>
            </div>
            <div class="wx-card p-5">
                <div class="grid grid-cols-2 gap-3">
                    @foreach([['Total Conversion', $stats['total']], ['Today', $stats['today']], ['This Month', $stats['month']], ['Storage Used', \Illuminate\Support\Number::fileSize($stats['storage'])]] as [$label, $value])
                        <div class="rounded-[20px] border border-white/[0.06] bg-white/[0.03] p-4">
                            <p class="text-sm text-slate-400">{{ $label }}</p>
                            <p class="mt-3 text-2xl font-semibold">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="wx-decor-glow mt-5 h-24 rounded-[20px] border border-white/[0.06] bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.12),transparent_12rem)]"></div>
            </div>
        </div>
    </section>

    <section id="converter" class="mx-auto max-w-5xl px-4 pb-14 sm:px-6 lg:px-8">
        @include('app.partials.converter-card')
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <h2 class="mb-4 text-xl font-semibold">Recent Conversion</h2>
        <div class="overflow-hidden wx-card">
            @include('app.partials.history-table', ['files' => $recent])
        </div>
    </section>
@endsection
