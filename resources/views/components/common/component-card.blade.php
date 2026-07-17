@props([
    'title',
    'desc' => '',
])

<div {{ $attributes->merge(['class' => 'wx-card-solid rounded-[24px]']) }}>
    <!-- Card Header -->
    <div class="px-6 py-5">
        <h3 class="text-base font-semibold tracking-tight">
            {{ $title }}
        </h3>
        @if($desc)
            <p class="mt-1 text-sm" style="color: var(--muted-foreground);">
                {{ $desc }}
            </p>
        @endif
    </div>

    <!-- Card Body -->
    <div class="border-t p-4 sm:p-6" style="border-color: var(--border);">
        <div class="space-y-6">
            {{ $slot }}
        </div>
    </div>
</div>
