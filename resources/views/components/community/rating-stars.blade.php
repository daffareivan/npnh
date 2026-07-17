@props([
    'rating' => 0,
    'size' => 18,
    'showNumber' => false,
    'showReviewCount' => false,
    'reviewCount' => 0,
    'showHalfStar' => true,
    'showTooltip' => false,
])

@php
    $ratingValue = max(0, min(5, (float) $rating));
    $starSize = is_numeric($size) ? $size.'px' : $size;
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}
    @if($showTooltip) title="{{ number_format($ratingValue, 1) }} out of 5" @endif
>
    <span class="inline-flex items-center gap-0.5">
        @for($index = 1; $index <= 5; $index++)
            @php
                $fill = max(0, min(1, $ratingValue - ($index - 1))) * 100;
                $clipId = 'rating-star-'.str_replace('.', '-', (string) $ratingValue).'-'.$index.'-'.uniqid();
            @endphp
            <span class="relative inline-grid transition duration-200 hover:scale-110" style="width: {{ $starSize }}; height: {{ $starSize }};">
                <svg class="absolute inset-0 text-slate-600" width="{{ $starSize }}" height="{{ $starSize }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="m12 2.8 2.83 5.74 6.33.92-4.58 4.47 1.08 6.31L12 17.26l-5.66 2.98 1.08-6.31-4.58-4.47 6.33-.92L12 2.8Z"/>
                </svg>
                <svg class="absolute inset-0 text-[#F59E0B]" width="{{ $starSize }}" height="{{ $starSize }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <defs>
                        <clipPath id="{{ $clipId }}">
                            <rect x="0" y="0" width="{{ $showHalfStar ? $fill : ($fill >= 100 ? 100 : 0) }}%" height="100%"/>
                        </clipPath>
                    </defs>
                    <path clip-path="url(#{{ $clipId }})" d="m12 2.8 2.83 5.74 6.33.92-4.58 4.47 1.08 6.31L12 17.26l-5.66 2.98 1.08-6.31-4.58-4.47 6.33-.92L12 2.8Z"/>
                </svg>
            </span>
        @endfor
    </span>
    @if($showNumber)
        <span class="text-sm font-semibold text-white">{{ number_format($ratingValue, 1) }}</span>
    @endif
    @if($showReviewCount)
        <span class="text-sm text-[#A3A3A3]">Based on {{ number_format((int) $reviewCount) }} Reviews</span>
    @endif
</span>
