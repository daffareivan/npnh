@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-medium text-white">Converter</p>
            <h1 class="mt-2 text-4xl font-semibold tracking-tight text-white">Convert your audio with optimized Roblox presets.</h1>
        </div>
        @include('app.partials.converter-card')
    </section>
@endsection
