@props(['label', 'value', 'meta' => null])

<div {{ $attributes->merge(['class' => 'wx-card-solid wx-hover-lift p-5']) }}>
    <p class="text-sm" style="color: var(--muted-foreground);">{{ $label }}</p>
    <p class="mt-3 text-2xl font-semibold tracking-tight">{{ $value }}</p>
    @if($meta)
        <p class="mt-2 text-xs">{{ $meta }}</p>
    @endif
</div>
