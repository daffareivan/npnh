@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Activity Log" />
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-4 flex gap-3"><input class="h-10 rounded-lg border border-gray-300 px-3" placeholder="Tanggal"><input class="h-10 rounded-lg border border-gray-300 px-3" placeholder="User"><input class="h-10 rounded-lg border border-gray-300 px-3" placeholder="Action"></div>
        <div class="space-y-3">
            @foreach($logs as $log)<div class="rounded-lg border border-gray-100 p-3 dark:border-gray-800"><span class="font-medium">{{ $log->event }}</span><span class="ml-3 text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</span></div>@endforeach
        </div>
        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
@endsection
