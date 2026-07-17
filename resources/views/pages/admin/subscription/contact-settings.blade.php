@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Contact Settings" />

    @if(session('status'))
        <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    <x-common.component-card title="Custom Plan Contact" desc="The Custom plan button opens the selected channel.">
        <form method="POST" action="{{ route('admin.subscription.contact-settings.update') }}" class="space-y-5">
            @csrf
            @method('PUT')
            <label>
                <span class="mb-1.5 block text-sm text-[#A3A3A3]">Preferred Channel</span>
                <select name="preferred_channel" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    @foreach(['whatsapp' => 'WhatsApp', 'telegram' => 'Telegram', 'discord' => 'Discord', 'email' => 'Email'] as $key => $label)
                        <option value="{{ $key }}" @selected(($settings['preferred_channel'] ?? 'whatsapp') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            @foreach(['whatsapp' => 'WhatsApp Number or URL', 'telegram' => 'Telegram Username or URL', 'discord' => 'Discord Invite URL', 'email' => 'Email'] as $key => $label)
                <label>
                    <span class="mb-1.5 block text-sm text-[#A3A3A3]">{{ $label }}</span>
                    <input name="{{ $key }}" value="{{ $settings[$key] ?? '' }}" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                </label>
            @endforeach
            <button class="wx-btn-primary px-5 py-3">Save Contact Settings</button>
        </form>
    </x-common.component-card>
@endsection
