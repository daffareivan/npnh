@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-20 text-center sm:px-6 lg:px-8">
        <h1 class="text-4xl font-semibold">{{ $heading }}</h1>
        <p class="mx-auto mt-4 max-w-2xl text-slate-300">{{ $copy }}</p>
    </section>
@endsection
