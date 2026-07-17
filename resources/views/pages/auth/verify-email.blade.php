@extends('layouts.auth-wx')

@section('content')
    <h2 class="text-2xl font-semibold tracking-tight">{{ __('auth.verify_email_title') }}</h2>
    <p class="mt-3 text-sm text-[#A3A3A3]">{{ __('auth.verify_email_copy') }}</p>
    <form method="POST" action="{{ route('verification.send') }}" class="mt-7">@csrf
        <button class="wx-btn-primary w-full px-4 py-3">{{ __('auth.resend_verification') }}</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf
        <button class="wx-btn-secondary w-full px-4 py-3">{{ __('auth.logout') }}</button>
    </form>
@endsection
