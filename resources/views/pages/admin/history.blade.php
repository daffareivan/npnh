@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Conversion Management" />
    <div class="overflow-hidden wx-card">
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
@endsection
