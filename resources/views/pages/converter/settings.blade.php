@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="__('converter.settings_title')" />

    <x-common.component-card :title="__('converter.settings_heading')" :desc="__('converter.settings_desc')">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-[#A3A3A3]">{{ __('converter.default_preset') }}</span>
                <select class="h-11 w-full rounded-2xl border border-white/[0.08] bg-white/[0.03] px-4 text-sm text-white outline-none focus:border-white/30">
                    @foreach($presets as $preset)
                        <option @selected($preset->is_default)>{{ $preset->name }} ({{ $preset->amplify_db }} dB)</option>
                    @endforeach
                </select>
            </label>

            @foreach([
                __('converter.storage_path') => $settings['storage_path'],
                __('converter.temporary_file_expiration') => $settings['temporary_expiration_hours'].' '.__('converter.hours'),
                __('converter.max_upload_size') => $settings['max_upload_size_mb'].' MB',
                __('converter.queue_name') => $settings['queue'],
                __('converter.auto_delete_file') => $settings['auto_delete_files'] ? __('common.enabled') : __('common.disabled'),
                __('converter.default_output_format') => strtoupper($settings['default_output_format']),
            ] as $label => $value)
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium text-[#A3A3A3]">{{ $label }}</span>
                    <input value="{{ $value }}" readonly class="h-11 w-full rounded-2xl border border-white/[0.08] bg-white/[0.03] px-4 text-sm text-white outline-none">
                </label>
            @endforeach
        </div>
    </x-common.component-card>
@endsection
