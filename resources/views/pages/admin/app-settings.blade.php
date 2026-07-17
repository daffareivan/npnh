@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="__('admin.app_settings')" />

    @if(session('status'))
        <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[480px_1fr]">
        <x-common.component-card :title="__('admin.brand_experience')" :desc="__('admin.brand_experience_desc')">
            <form method="POST" action="{{ route('admin.app-settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <label class="flex items-center justify-between rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                    <span>
                        <span class="block font-medium text-white">{{ __('admin.enable_intro_animation') }}</span>
                        <span class="text-sm text-[#A3A3A3]">{{ __('admin.enable_intro_animation_help') }}</span>
                    </span>
                    <input name="{{ \App\Models\AppSetting::INTRO_ANIMATION_ENABLED }}" type="checkbox" value="1" @checked($introAnimationEnabled)>
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-sm text-[#A3A3A3]">{{ __('admin.default_theme') }}</span>
                    <select name="{{ \App\Models\AppSetting::THEME_DEFAULT }}" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                        @foreach(['system' => __('ui.system'), 'dark' => __('ui.dark'), 'light' => __('ui.light')] as $value => $label)
                            <option value="{{ $value }}" @selected($themeDefault === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-sm text-[#A3A3A3]">{{ __('admin.default_language') }}</span>
                    <select name="{{ \App\Models\AppSetting::LOCALE_DEFAULT }}" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                        <option value="en" @selected($localeDefault === 'en')>English</option>
                        <option value="id" @selected($localeDefault === 'id')>Indonesia</option>
                    </select>
                </label>

                <label class="flex items-center justify-between rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                    <span>
                        <span class="block font-medium text-white">{{ __('admin.allow_theme_switch') }}</span>
                        <span class="text-sm text-[#A3A3A3]">{{ __('admin.allow_theme_switch_help') }}</span>
                    </span>
                    <input name="{{ \App\Models\AppSetting::ALLOW_THEME_SWITCH }}" type="checkbox" value="1" @checked($allowThemeSwitch)>
                </label>

                <label class="flex items-center justify-between rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                    <span>
                        <span class="block font-medium text-white">{{ __('admin.allow_language_switch') }}</span>
                        <span class="text-sm text-[#A3A3A3]">{{ __('admin.allow_language_switch_help') }}</span>
                    </span>
                    <input name="{{ \App\Models\AppSetting::ALLOW_LANGUAGE_SWITCH }}" type="checkbox" value="1" @checked($allowLanguageSwitch)>
                </label>

                <button class="wx-btn-primary px-5 py-3">{{ __('admin.save_settings') }}</button>
            </form>
        </x-common.component-card>

        <x-common.component-card :title="__('admin.motion_rules')">
            <div class="grid gap-3 text-sm text-[#A3A3A3]">
                <p class="rounded-2xl border border-white/[0.06] bg-black/20 p-4">{{ __('admin.intro_rule_initial') }}</p>
                <p class="rounded-2xl border border-white/[0.06] bg-black/20 p-4">{{ __('admin.intro_rule_reduced_motion') }}</p>
                <p class="rounded-2xl border border-white/[0.06] bg-black/20 p-4">{{ __('admin.intro_rule_authenticated') }}</p>
            </div>
        </x-common.component-card>
    </div>
@endsection
