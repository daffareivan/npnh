@props(['code' => null])

<section
    {{ $attributes->merge(['class' => 'wx-reveal wx-glow relative w-full max-w-lg rounded-[28px] border border-border bg-card/80 p-8 text-center backdrop-blur-xl sm:p-10']) }}
    aria-labelledby="error-title"
>
    {{ $slot }}
</section>
