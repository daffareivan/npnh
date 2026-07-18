@props(['enabled' => true])

@if($enabled)
    <div
        x-data="brandIntro()"
        x-show="visible"
        x-cloak
        class="fixed inset-0 z-[999999] grid place-items-center overflow-hidden bg-[#09090B]"
        :class="{ 'wx-intro-exit': exiting }"
        aria-hidden="true"
    >
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_44%,rgba(139,92,246,.18),transparent_20rem),radial-gradient(circle_at_42%_58%,rgba(59,130,246,.10),transparent_24rem)]"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[.06] [background-image:radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] [background-size:18px_18px]"></div>

        <div class="relative grid place-items-center text-center">
            <div class="wx-intro-glow absolute size-48 rounded-full bg-[#8B5CF6]/15 blur-3xl"></div>
            <div class="wx-intro-logo relative grid size-24 place-items-center overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.055] text-white shadow-[0_24px_100px_rgba(139,92,246,.18)] backdrop-blur">
                <img src="{{ asset('images/logo.png') }}" alt="NPNHCREATIVE" class="size-full object-cover">
            </div>
            <h1 class="wx-intro-name mt-6 text-xl font-semibold text-white">NPNHCREATIVE</h1>
            <div class="wx-intro-wave mt-5 flex h-8 items-end gap-1.5">
                @foreach([12,20,30,18,26,14,32,22,16,28,19,24] as $height)
                    <span class="wx-intro-wave-bar w-1.5 rounded-full bg-white/80" style="height: {{ $height }}px; animation-delay: {{ $loop->index * 70 }}ms"></span>
                @endforeach
            </div>
            <p class="wx-intro-tagline mt-5 text-sm text-[#A3A3A3]">Fast Audio Converter for Roblox</p>
        </div>
    </div>
@endif
