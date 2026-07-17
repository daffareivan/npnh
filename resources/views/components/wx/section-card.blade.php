@props(['title' => null, 'subtitle' => null])

<section {{ $attributes->merge(['class' => 'wx-card p-5 sm:p-6']) }}>
    @if($title || $subtitle)
        <div class="mb-5">
            @if($title)<h2 class="text-xl font-semibold tracking-tight">{{ $title }}</h2>@endif
            @if($subtitle)<p class="mt-1 text-sm" style="color: var(--muted-foreground);">{{ $subtitle }}</p>@endif
        </div>
    @endif
    {{ $slot }}
</section>
