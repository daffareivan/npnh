@props(['href' => null, 'variant' => 'secondary'])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-full px-5 py-3 text-sm font-semibold transition duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background';
    $variantClasses = $variant === 'primary'
        ? 'bg-primary text-primary-foreground hover:opacity-90 hover:-translate-y-0.5 shadow-[0_14px_44px_color-mix(in_srgb,var(--primary)_22%,transparent)]'
        : 'border border-border bg-transparent text-foreground hover:bg-muted hover:-translate-y-0.5';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "{$base} {$variantClasses}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => "{$base} {$variantClasses}"]) }}>
        {{ $slot }}
    </button>
@endif
