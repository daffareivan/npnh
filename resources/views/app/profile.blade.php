@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-7">
            <p class="text-sm font-medium text-white">Profile</p>
            <h1 class="mt-2 text-4xl font-semibold tracking-tight">Profile User</h1>
            <p class="mt-2 text-[#A3A3A3]">Manage identity, password, Google account, and account security.</p>
        </div>

        @if(session('status'))
            <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
        @endif

        <div class="grid gap-5 lg:grid-cols-[1fr_360px]">
            <div class="space-y-5">
                <x-wx.section-card title="User Information">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="grid gap-4">@csrf @method('PUT')
                        <div class="flex items-center gap-4">
                            @php($avatar = auth()->user()->avatar ?: auth()->user()->avatar_path)
                            <div class="grid size-20 place-items-center overflow-hidden rounded-[24px] border border-white/10 bg-white/[0.05] text-2xl font-semibold text-white">
                                @if($avatar)<img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}" class="h-full w-full object-cover" alt="Avatar">@else{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}@endif
                            </div>
                            <label class="wx-btn-secondary px-4 py-2.5 text-sm">Change Avatar<input type="file" name="avatar" class="hidden" accept="image/*"></label>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($badges ?? [] as $badge)
                                <x-community.badge :badge="$badge" />
                            @endforeach
                        </div>
                        <label><span class="text-sm text-[#E5E5E5]">Nama</span><input name="name" value="{{ old('name', auth()->user()->name) }}" class="mt-2 w-full rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none focus:border-white/30"></label>
                        <label><span class="text-sm text-[#E5E5E5]">Email</span><input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" class="mt-2 w-full rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none focus:border-white/30"></label>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-[#A3A3A3]">Email verified:</span>
                            <x-wx.badge :tone="auth()->user()->hasVerifiedEmail() ? 'success' : 'danger'">{{ auth()->user()->hasVerifiedEmail() ? 'Verified' : 'Not verified' }}</x-wx.badge>
                        </div>
                        <button class="wx-btn-primary w-fit px-5 py-3">Save Profile</button>
                    </form>
                </x-wx.section-card>

                <x-wx.section-card title="Change Password">
                    <form method="POST" action="{{ route('profile.password') }}" class="grid gap-4">@csrf @method('PUT')
                        <input name="current_password" type="password" placeholder="Current password" class="rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none focus:border-white/30">
                        <input name="password" type="password" placeholder="New password" class="rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none focus:border-white/30">
                        <input name="password_confirmation" type="password" placeholder="Confirm new password" class="rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none focus:border-white/30">
                        <button class="wx-btn-primary w-fit px-5 py-3">Update Password</button>
                    </form>
                </x-wx.section-card>

                <x-wx.section-card title="Delete Account" subtitle="This action permanently removes your account.">
                    <form method="POST" action="{{ route('profile.destroy') }}" class="flex flex-col gap-3 sm:flex-row">@csrf @method('DELETE')
                        <input name="password" type="password" placeholder="Confirm password" class="flex-1 rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-white outline-none">
                        <button class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-5 py-3 text-rose-200">Delete Account</button>
                    </form>
                </x-wx.section-card>
            </div>

            <div class="space-y-5">
                <x-wx.section-card title="Activity">
                    <div class="space-y-4">
                        <x-wx.stat-card label="Conversion Count" :value="$stats['count']" />
                        <x-wx.stat-card label="Last Conversion" :value="$stats['last']?->created_at?->diffForHumans() ?? '-'" />
                        <x-wx.stat-card label="Storage Usage" :value="\Illuminate\Support\Number::fileSize($stats['storage'])" />
                    </div>
                </x-wx.section-card>

                <x-wx.section-card title="Connected Accounts">
                    <div class="space-y-4">
                        <div class="rounded-[20px] border border-white/[0.06] bg-black/20 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold">Google</p>
                                    <p class="mt-1 text-sm text-[#A3A3A3]">{{ auth()->user()->provider === 'google' ? 'Connected' : 'Not Connected' }}</p>
                                </div>
                                @if(auth()->user()->provider === 'google')
                                    <form method="POST" action="{{ route('profile.google.unlink') }}">@csrf @method('DELETE')<button class="wx-btn-secondary px-4 py-2.5 text-sm">Disconnect</button></form>
                                @else
                                    <a href="{{ route('google.redirect') }}" class="wx-btn-primary px-4 py-2.5 text-sm">Connect</a>
                                @endif
                            </div>
                        </div>
                        <div class="rounded-[20px] border border-white/[0.06] bg-black/20 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold">Roblox</p>
                                    <p class="mt-1 text-sm text-[#A3A3A3]">{{ $robloxAccount ? 'Connected' : 'Not Connected' }}</p>
                                </div>
                                @if($robloxAccount)
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('roblox.switch') }}">@csrf<button class="wx-btn-primary px-4 py-2.5 text-sm">Switch</button></form>
                                        <form method="POST" action="{{ route('roblox.disconnect') }}">@csrf @method('DELETE')<button class="wx-btn-secondary px-4 py-2.5 text-sm">Disconnect</button></form>
                                    </div>
                                @else
                                    <a href="{{ route('roblox.connect') }}" class="wx-btn-primary px-4 py-2.5 text-sm">Connect</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-wx.section-card>
            </div>
        </div>
    </section>
@endsection
