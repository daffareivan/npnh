@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-white">History</p>
                <h1 class="mt-2 text-4xl font-semibold tracking-tight text-white">Conversion history</h1>
                <p class="mt-2 text-[#A3A3A3]">Search, download, delete, or reconvert previous audio files.</p>
            </div>
            <a href="{{ route('app.converter') }}" class="wx-btn-primary px-5 py-3 text-center">New Conversion</a>
        </div>

        <form class="wx-card mb-5 grid gap-3 p-3 sm:grid-cols-[1fr_180px_auto]">
            <input name="search" value="{{ request('search') }}" class="rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-sm text-white outline-none placeholder:text-[#A3A3A3] focus:border-[#FFFFFF]/50" placeholder="Search file">
            <select name="status" class="rounded-2xl border border-white/8 bg-black/20 px-4 py-3 text-sm text-white outline-none">
                <option value="">All status</option>
                @foreach(['uploaded','pending','analyzing','converting','encoding','finished','failed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button class="wx-btn-secondary px-5 py-3">Filter</button>
        </form>

        <div class="grid gap-3">
            @forelse($events as $event)
                @php($file = $event['audio_file'])
                @if($event['type'] === 'conversion')
                    @php($fileCount = $file->files->count() ?: 1)
                    @php($totalDuration = $file->files->isNotEmpty() ? $file->files->sum('duration') : $file->duration)
                    <article class="wx-card-solid wx-hover-lift p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="truncate text-lg font-semibold text-white">{{ $file->original_name }}</h2>
                                    <x-wx.badge :tone="$file->status->value === 'finished' ? 'success' : ($file->status->value === 'failed' ? 'danger' : 'default')">{{ ucfirst($file->status->value) }}</x-wx.badge>
                                    @if($fileCount > 1)
                                        <x-wx.badge tone="default">{{ $fileCount }} {{ __('converter.file_count') }}</x-wx.badge>
                                    @endif
                                </div>
                                <div class="mt-3 flex flex-wrap gap-3 text-sm text-[#A3A3A3]">
                                    <span>{{ $file->created_at->format('M d, Y H:i') }}</span>
                                    <span>Preset {{ $file->speed }}x</span>
                                    <span>{{ \Illuminate\Support\Number::fileSize($file->output_size ?: $file->original_size) }}</span>
                                    <span>{{ $totalDuration ? gmdate('i:s', (int) $totalDuration) : 'Duration pending' }}</span>
                                    <span class="uppercase tracking-[0.12em]">UPLOAD FROM NPNH CREATIVE</span>
                                    @if($fileCount === 1)
                                        <span>Roblox: {{ ucfirst($file->roblox_status ?? 'pending') }}</span>
                                        @if($file->roblox_asset_id)
                                            <span>Asset ID {{ $file->roblox_asset_id }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if($fileCount > 1)
                                    <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('api.converter.download-all', now()->addMinutes(15), $file) }}" class="wx-btn-primary px-4 py-2.5 text-sm">{{ __('converter.download_all') }}</a>
                                @elseif($file->files->first())
                                    <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('api.converter.files.download', now()->addMinutes(15), $file->files->first()) }}" class="wx-btn-primary px-4 py-2.5 text-sm">Download</a>
                                @endif
                                @if($file->roblox_creator_url)
                                    <a href="{{ $file->roblox_creator_url }}" target="_blank" rel="noopener" class="wx-btn-secondary px-4 py-2.5 text-sm">Open Creator Hub</a>
                                @endif
                                <a href="{{ route('app.converter') }}" class="wx-btn-secondary px-4 py-2.5 text-sm">Reconvert</a>
                                <button onclick="axios.delete('/api/converter/history/{{ $file->id }}').then(() => location.reload())" class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-2.5 text-sm text-rose-200 transition hover:bg-rose-400/15">Delete</button>
                            </div>
                        </div>
                    </article>
                @elseif($file)
                    @php($conversionFile = $event['conversion_file'] ?? null)
                    <article class="wx-card p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-white/[0.06] text-emerald-300">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-white">
                                        {{ __('converter.downloaded') }}
                                        {{ $conversionFile ? $conversionFile->label().' — '.$file->original_name : $file->original_name }}
                                    </p>
                                    <p class="text-xs text-[#A3A3A3]">{{ $event['created_at']->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            @if($conversionFile)
                                <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('api.converter.files.download', now()->addMinutes(15), $conversionFile) }}" class="wx-btn-secondary shrink-0 px-4 py-2 text-xs">Download Again</a>
                            @elseif($file->output_path)
                                <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('api.converter.download', now()->addMinutes(15), $file) }}" class="wx-btn-secondary shrink-0 px-4 py-2 text-xs">Download Again</a>
                            @endif
                        </div>
                    </article>
                @endif
            @empty
                <div class="wx-card grid min-h-72 place-items-center p-8 text-center">
                    <div>
                        <div class="mx-auto grid size-14 place-items-center rounded-2xl bg-white/[0.05] text-white">
                            <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 7h16"/><path d="M4 12h10"/><path d="M4 17h7"/></svg>
                        </div>
                        <h2 class="mt-5 text-xl font-semibold">No conversions yet</h2>
                        <p class="mt-2 text-[#A3A3A3]">Upload your first audio file to build your history.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $events->links() }}</div>
    </section>
@endsection
