@extends('layouts.auth-wx')

@section('content')
    <h2 class="text-2xl font-semibold tracking-tight">{{ __('auth.forgot_password_title') }}</h2>
    <p class="mt-2 text-sm text-[#A3A3A3]">{{ __('auth.forgot_password_copy') }}</p>
    <form method="POST" action="{{ route('password.email') }}" class="mt-7 space-y-4">@csrf
        <label class="block"><span class="text-sm text-[#E5E5E5]">{{ __('auth.email') }}</span><input name="email" type="email" required class="mt-2 w-full rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none focus:border-white/30"></label>
        @error('email')<p class="text-sm text-rose-300">{{ $message }}</p>@enderror
        <button class="wx-btn-primary w-full px-4 py-3">{{ __('auth.send_reset_link') }}</button>
    </form>
@endsection
