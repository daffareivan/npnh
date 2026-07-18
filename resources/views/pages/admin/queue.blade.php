@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="__('queue.title')" />
    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-4">
        @foreach([[__('queue.running'), __('queue.queue_worker_required')], [__('queue.waiting'), $waiting], [__('queue.failed'), $failed], [__('queue.completed'), __('queue.tracked_in_history')]] as [$label, $value])
            <div class="wx-card p-5"><p class="text-sm text-[#A3A3A3]">{{ $label }}</p><p class="mt-3 text-xl font-semibold text-white">{{ $value }}</p></div>
        @endforeach
    </div>
    <x-common.component-card class="mt-6" :title="__('queue.actions')">
        <div class="flex flex-col gap-3 sm:flex-row"><button class="wx-btn-primary w-full px-4 py-2 sm:w-auto">{{ __('queue.retry_failed_job') }}</button><button class="wx-btn-secondary w-full px-4 py-2 sm:w-auto">{{ __('queue.clear_queue') }}</button></div>
    </x-common.component-card>
@endsection
