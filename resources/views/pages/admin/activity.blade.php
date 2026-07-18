@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Activity Log" />
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <form class="mb-4 flex flex-wrap gap-3">
            <input type="date" name="date" value="{{ request('date') }}" class="h-10 rounded-lg border border-gray-300 px-3 dark:border-gray-700 dark:bg-white/[0.03]" placeholder="Tanggal">
            <input type="text" name="user" value="{{ request('user') }}" class="h-10 rounded-lg border border-gray-300 px-3 dark:border-gray-700 dark:bg-white/[0.03]" placeholder="User">
            <input type="text" name="event" value="{{ request('event') }}" class="h-10 rounded-lg border border-gray-300 px-3 dark:border-gray-700 dark:bg-white/[0.03]" placeholder="Action">
            <button class="wx-btn-secondary px-5">Filter</button>
        </form>
        <div class="space-y-3">
            @forelse($logs as $log)
                <div class="rounded-lg border border-gray-100 p-3 dark:border-gray-800"><span class="font-medium">{{ $log->event }}</span><span class="ml-3 text-sm text-gray-500">{{ $log->user?->name ?? 'System' }}</span><span class="ml-3 text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</span></div>
            @empty
                <p class="text-sm text-gray-500">No activity found.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
@endsection
