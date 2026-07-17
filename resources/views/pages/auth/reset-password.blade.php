@extends('layouts.auth-wx')

@section('content')
    <h2 class="text-2xl font-semibold tracking-tight">{{ __('auth.reset_password_title') }}</h2>
    <form method="POST" action="{{ route('password.update') }}" class="mt-7 space-y-4">@csrf
        <input type="hidden" name="token" value="{{ $token }}"><input type="hidden" name="email" value="{{ $email }}">
        <label class="block"><span class="text-sm text-[#E5E5E5]">{{ __('auth.new_password') }}</span><input name="password" type="password" required class="mt-2 w-full rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none focus:border-white/30"></label>
        <label class="block"><span class="text-sm text-[#E5E5E5]">{{ __('auth.confirm_password') }}</span><input name="password_confirmation" type="password" required class="mt-2 w-full rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none focus:border-white/30"></label>
        @error('email')<p class="text-sm text-rose-300">{{ $message }}</p>@enderror
        @error('password')<p class="text-sm text-rose-300">{{ $message }}</p>@enderror
        <button class="wx-btn-primary w-full px-4 py-3">{{ __('auth.reset_password') }}</button>
    </form>
@endsection
