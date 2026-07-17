@props(['badge'])

@php
    $slug = is_string($badge) ? $badge : $badge?->slug;
    $name = strtoupper(is_string($badge) ? $badge : $badge?->name);
    $icon = is_string($badge) ? $badge : $badge?->icon;
    $styles = [
        'free' => 'bg-[linear-gradient(#111214,#111214)_padding-box,linear-gradient(135deg,rgba(107,114,128,.45),rgba(255,255,255,.08))_border-box] border-transparent text-[#9CA3AF]',
        'standard' => 'bg-[linear-gradient(#0B111E,#0B111E)_padding-box,linear-gradient(135deg,rgba(59,130,246,.70),rgba(147,197,253,.12))_border-box] border-transparent text-[#60A5FA] shadow-[0_0_18px_rgba(59,130,246,.12)]',
        'premium' => 'bg-[linear-gradient(#150D1F,#150D1F)_padding-box,linear-gradient(135deg,rgba(216,180,254,.80),rgba(168,85,247,.20),rgba(255,255,255,.08))_border-box] border-transparent text-[#C084FC] shadow-[0_0_26px_rgba(168,85,247,.28)]',
        'enterprise' => 'bg-[linear-gradient(#1D1306,#1D1306)_padding-box,linear-gradient(135deg,rgba(251,191,36,.85),rgba(245,158,11,.18),rgba(255,255,255,.10))_border-box] border-transparent text-[#FBBF24] shadow-[0_0_28px_rgba(245,158,11,.28)]',
        'verified' => 'bg-[linear-gradient(#0A1710,#0A1710)_padding-box,linear-gradient(135deg,rgba(34,197,94,.75),rgba(134,239,172,.12))_border-box] border-transparent text-[#4ADE80] shadow-[0_0_18px_rgba(34,197,94,.14)]',
        'founder' => 'bg-[linear-gradient(135deg,rgba(245,158,11,.28),rgba(250,204,21,.11))_padding-box,linear-gradient(135deg,rgba(253,230,138,.85),rgba(245,158,11,.25))_border-box] border-transparent text-[#FDE68A] shadow-[0_0_28px_rgba(245,158,11,.28)]',
        'developer' => 'bg-[linear-gradient(#07171B,#07171B)_padding-box,linear-gradient(135deg,rgba(6,182,212,.75),rgba(103,232,249,.12))_border-box] border-transparent text-[#67E8F9] shadow-[0_0_18px_rgba(6,182,212,.16)]',
    ];
    $style = $styles[$slug] ?? 'bg-white/[0.06] border-white/10 text-white/80';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex h-7 items-center gap-1.5 rounded-full border px-3 text-xs font-semibold uppercase backdrop-blur-md {$style}"]) }}>
    <x-community.icon :name="$icon ?: $slug" class="size-4" />
    <span>{{ $name }}</span>
</span>
