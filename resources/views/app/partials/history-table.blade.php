<div class="hidden lg:block overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="border-b border-white/10 text-left text-slate-400">
            <tr><th class="p-4">File</th><th class="p-4">Preset</th><th class="p-4">Status</th><th class="p-4">Download</th><th class="p-4">Created</th></tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            @forelse($files as $file)
                <tr>
                    <td class="p-4 font-medium">{{ $file->original_name }}</td>
                    <td class="p-4 text-slate-300">{{ $file->speed }}x</td>
                    <td class="p-4"><x-wx.badge :tone="$file->status->value === 'finished' ? 'success' : 'default'">{{ ucfirst($file->status->value) }}</x-wx.badge></td>
                    <td class="p-4">@if($file->output_path)<a class="text-white/80" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('api.converter.download', now()->addMinutes(15), $file) }}">Download</a>@else<span class="text-slate-500">-</span>@endif</td>
                    <td class="p-4 text-slate-400">{{ $file->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-8 text-center text-slate-500">No conversions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="grid gap-3 p-4 lg:hidden">
    @forelse($files as $file)
        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
            <div class="flex items-start justify-between gap-3">
                <p class="font-medium text-white">{{ $file->original_name }}</p>
                <x-wx.badge :tone="$file->status->value === 'finished' ? 'success' : 'default'">{{ ucfirst($file->status->value) }}</x-wx.badge>
            </div>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-400">
                <span>{{ $file->speed }}x</span>
                <span>{{ $file->created_at->format('M d, Y') }}</span>
            </div>
            @if($file->output_path)
                <a class="mt-3 inline-block text-sm text-white/80" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('api.converter.download', now()->addMinutes(15), $file) }}">Download</a>
            @endif
        </div>
    @empty
        <p class="p-4 text-center text-slate-500">No conversions yet.</p>
    @endforelse
</div>
