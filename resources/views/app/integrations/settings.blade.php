@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-7">
            <p class="text-sm font-medium text-white">Settings</p>
            <h1 class="mt-2 text-4xl font-semibold tracking-tight">Integration Settings</h1>
            <p class="mt-2 text-[#A3A3A3]">Configure creator workflow preferences for Google and Roblox.</p>
        </div>

        <div class="wx-card p-6 sm:p-8">
            <h2 class="text-2xl font-semibold">Roblox</h2>
            <div class="mt-6 grid gap-4">
                @foreach([
                    ['Automatically Open Creator Hub', 'Open Roblox Creator Hub after conversion is complete.'],
                    ['Auto Copy Asset ID', 'Reserved for future official asset upload support.'],
                    ['Use Converted Filename', 'Use the converted audio filename as the default asset name.'],
                ] as [$label, $copy])
                    <label class="flex items-center justify-between gap-4 rounded-[20px] border border-white/[0.06] bg-black/20 p-4">
                        <span>
                            <span class="block font-semibold">{{ $label }}</span>
                            <span class="mt-1 block text-sm text-[#A3A3A3]">{{ $copy }}</span>
                        </span>
                        <input type="checkbox" class="size-5 rounded border-white/10 bg-black/20" @checked($label !== 'Auto Copy Asset ID')>
                    </label>
                @endforeach
                <label class="block rounded-[20px] border border-white/[0.06] bg-black/20 p-4">
                    <span class="text-sm text-[#E5E5E5]">Default Asset Name</span>
                    <input value="Use Converted Filename" class="mt-2 w-full rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none focus:border-white/30">
                </label>
            </div>
        </div>
    </section>
@endsection
