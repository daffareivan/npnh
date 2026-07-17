@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="__('converter.history_title')" />

    <div class="overflow-hidden wx-card">
        <div class="px-5 py-4 sm:px-6">
            <h3 class="text-base font-semibold tracking-tight text-white">{{ __('converter.history_heading') }}</h3>
        </div>
        <div class="max-w-full overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-y border-white/[0.06]">
                    <tr>
                        @foreach([
                            __('converter.file_name'),
                            __('converter.original_size'),
                            __('converter.output_size'),
                            __('converter.speed'),
                            __('converter.amplify'),
                            __('common.status'),
                            __('common.created_at'),
                            __('common.action'),
                        ] as $heading)
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-[0.14em] text-[#6B7280]">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.06]">
                    @forelse($files as $file)
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-white">{{ $file->original_name }}</td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ \Illuminate\Support\Number::fileSize($file->original_size) }}</td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $file->output_size ? \Illuminate\Support\Number::fileSize($file->output_size) : '-' }}</td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $file->speed }}x</td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $file->amplify_db }} dB</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $file->status->value === 'finished' ? 'bg-success-50 text-success-600' : 'bg-warning-50 text-warning-600' }}">{{ ucfirst($file->status->value) }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-[#A3A3A3]">{{ $file->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-2 text-sm">
                                    @if($file->output_path)
                                        <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('api.converter.download', now()->addMinutes(15), $file) }}" class="text-white hover:text-[#E5E5E5]">{{ __('common.download') }}</a>
                                    @endif
                                    <a href="{{ route('app.converter') }}" class="text-[#A3A3A3] hover:text-white">{{ __('common.reconvert') }}</a>
                                    <button type="button" onclick="axios.delete('/api/converter/history/{{ $file->id }}').then(() => location.reload())" class="text-error-500">{{ __('common.delete') }}</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-sm text-[#A3A3A3]">{{ __('converter.no_conversions') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-white/[0.06] px-5 py-4">
            {{ $files->links() }}
        </div>
    </div>
@endsection
