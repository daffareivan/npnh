@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Conversion Management" />
    <form class="wx-card mb-5 grid gap-3 p-3 sm:grid-cols-[1fr_180px_auto]">
        <input name="search" value="{{ request('search') }}" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none placeholder:text-[#A3A3A3] focus:border-white/30" placeholder="Search file or user">
        <select name="status" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
            <option value="">All status</option>
            @foreach(\App\Enums\ConversionStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ ucfirst($status->value) }}</option>
            @endforeach
        </select>
        <button class="wx-btn-secondary px-5 py-3">Filter</button>
    </form>
    <div class="hidden overflow-hidden wx-card lg:block">
        <table class="min-w-full">
            <thead class="border-b border-white/[0.06]"><tr>@foreach(['User','File','Preset','Status','Processing Time','Created','Action'] as $h)<th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-[0.14em] text-[#6B7280]">{{ $h }}</th>@endforeach</tr></thead>
            <tbody class="divide-y divide-white/[0.06]">
                @foreach($files as $file)
                    <tr>
                        <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $file->user?->name ?? 'Guest' }}</td>
                        <td class="px-5 py-4 text-sm font-medium text-white">{{ $file->original_name }}</td>
                        <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $file->speed }}x</td>
                        <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ ucfirst($file->status->value) }}</td>
                        <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $file->finished_at ? $file->created_at->diffInSeconds($file->finished_at).'s' : '-' }}</td>
                        <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $file->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-5 py-4 text-sm">@if($file->output_path)<a class="text-white hover:text-[#E5E5E5]" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('api.converter.download', now()->addMinutes(15), $file) }}">Download</a>@endif <button onclick="axios.delete('/api/converter/history/{{ $file->id }}').then(() => location.reload())" class="ml-3 text-error-500">Delete</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $files->links() }}</div>
    </div>

    <div class="grid gap-3 lg:hidden">
        @foreach($files as $file)
            <div class="wx-card p-4">
                <div class="flex items-start justify-between gap-3">
                    <p class="min-w-0 flex-1 truncate text-sm font-medium text-white">{{ $file->original_name }}</p>
                    <span class="shrink-0 text-xs text-[#A3A3A3]">{{ ucfirst($file->status->value) }}</span>
                </div>
                <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-xs text-[#A3A3A3]">
                    <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">User</dt><dd class="mt-0.5 text-white/80">{{ $file->user?->name ?? 'Guest' }}</dd></div>
                    <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Preset</dt><dd class="mt-0.5 text-white/80">{{ $file->speed }}x</dd></div>
                    <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Processing</dt><dd class="mt-0.5 text-white/80">{{ $file->finished_at ? $file->created_at->diffInSeconds($file->finished_at).'s' : '-' }}</dd></div>
                    <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Created</dt><dd class="mt-0.5 text-white/80">{{ $file->created_at->format('M d, Y H:i') }}</dd></div>
                </dl>
                <div class="mt-3 flex items-center gap-3 border-t border-white/[0.06] pt-3 text-sm">
                    @if($file->output_path)<a class="text-white hover:text-[#E5E5E5]" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('api.converter.download', now()->addMinutes(15), $file) }}">Download</a>@endif
                    <button onclick="axios.delete('/api/converter/history/{{ $file->id }}').then(() => location.reload())" class="text-error-500">Delete</button>
                </div>
            </div>
        @endforeach
        <div class="wx-card p-4">{{ $files->links() }}</div>
    </div>
@endsection
