@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if(session('status'))
            <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.06] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
        @endif

        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.24em] text-white/50">Credit-Based Subscription</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight text-white">Choose credits, keep every feature.</h1>
                <p class="mt-3 max-w-2xl text-[#A3A3A3]">Semua paket membuka fitur yang sama. Credits hanya dipakai saat download hasil konversi atau upload ke Roblox.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">
                {{ $currentPlan->name }} <span class="mx-2 text-white/30">|</span> {{ $creditBalance }} Credits
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($plans as $plan)
                @php
                    $badge = match($plan->slug) {
                        'free' => $currentPlan->id === $plan->id ? 'Current Plan' : null,
                        'standard' => 'Most Popular',
                        'premium' => 'Best Value',
                        default => null,
                    };
                    $button = match($plan->slug) {
                        'free' => 'Get Started',
                        'standard' => 'Buy Standard',
                        'premium' => 'Buy Premium',
                        default => 'Contact Admin',
                    };
                @endphp
                <div class="group relative rounded-[28px] border {{ $plan->slug === 'standard' ? 'border-white/25 bg-white/[0.09]' : 'border-white/[0.08] bg-[#111214]' }} p-6 shadow-[0_18px_80px_rgba(0,0,0,.28)] transition duration-300 hover:-translate-y-1 hover:border-white/25">
                    @if($badge)
                        <span class="absolute right-5 top-5 rounded-full bg-white px-3 py-1 text-xs font-semibold text-black">{{ $badge }}</span>
                    @endif
                    <p class="text-sm uppercase tracking-[0.22em] text-[#A3A3A3]">{{ $plan->name }}</p>
                    <h2 class="mt-5 text-3xl font-semibold text-white">{{ $plan->formattedPrice() }}</h2>
                    <p class="mt-2 text-sm text-[#A3A3A3]">{{ $plan->credits ? $plan->credits.' Credits' : 'Unlimited Credits' }}</p>

                    <ul class="mt-8 space-y-3 text-sm text-white/85">
                        @foreach($plan->features ?? [] as $feature)
                            <li class="flex gap-3"><span class="text-white">✓</span><span>{{ $feature }}</span></li>
                        @endforeach
                    </ul>

                    @if($plan->is_custom)
                        <a href="{{ $contactUrl }}" target="_blank" class="mt-8 inline-flex w-full justify-center wx-btn-secondary px-5 py-3">{{ $button }}</a>
                    @else
                        <form method="POST" action="{{ route('app.plans.checkout', $plan) }}" class="mt-8">
                            @csrf
                            <button @disabled($plan->price === 0 && $currentPlan->id === $plan->id) class="w-full {{ $plan->slug === 'premium' || $plan->slug === 'standard' ? 'wx-btn-primary' : 'wx-btn-secondary' }} px-5 py-3 disabled:cursor-not-allowed disabled:opacity-60">{{ $currentPlan->id === $plan->id ? 'Current Plan' : $button }}</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
@endsection
