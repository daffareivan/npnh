@props(['tone' => 'default'])

@php
    $classes = [
        'default' => 'wx-pill',
        'success' => 'border-emerald-400/25 bg-emerald-400/10 text-emerald-500',
        'danger' => 'border-rose-400/20 bg-rose-400/10 text-rose-200',
    ][$tone] ?? 'wx-pill';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium '.$classes]) }}>{{ $slot }}</span>
