@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="NPNHCREATIVE" />

    <div class="mx-auto max-w-5xl">
        @include('app.partials.converter-card')
    </div>
@endsection
