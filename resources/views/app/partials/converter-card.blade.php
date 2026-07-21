<div x-data="wxConverter({ presets: @js($presets), defaultPresetId: {{ $defaultPreset?->id ?? $presets->first()?->id }}, creditBalance: {{ $creditBalance ?? auth()->user()?->credits_balance ?? 0 }}, downloadCost: {{ $downloadCost ?? 1 }}, robloxUploadCost: {{ $robloxUploadCost ?? 2 }} })" class="relative overflow-hidden rounded-[28px] border border-white/[0.05] bg-[#111214] p-5 shadow-[0_10px_60px_rgba(0,0,0,.35)] sm:p-8">
    <div class="wx-decor-glow pointer-events-none absolute inset-x-0 top-0 h-48 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.08),transparent_18rem)]"></div>

    <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.24em] text-[#A3A3A3]">Active Preset</p>
            <div class="mt-2 flex items-end gap-3">
                <span class="text-4xl font-semibold tracking-tight" x-text="activePreset.name"></span>
                <span class="pb-1 text-[#A3A3A3]"><span x-text="activePreset.amplify_db"></span> dB</span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-1 rounded-full border border-white/[0.06] bg-black/25 p-1">
            @foreach($presets as $preset)
                <button type="button" @click="selectPreset({{ $preset->id }})" class="rounded-full px-5 py-3 text-center transition duration-250"
                    :class="activePreset.id === {{ $preset->id }} ? 'bg-white text-black shadow-[0_10px_30px_rgba(255,255,255,.08)]' : 'text-[#A3A3A3] hover:bg-white/[0.05] hover:text-white'">
                    <span class="block font-semibold">{{ $preset->name }}</span>
                    <span class="text-xs opacity-70">{{ $preset->amplify_db }} dB</span>
                </button>
            @endforeach
        </div>
    </div>

    <div @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="handleDrop($event)" @click="$refs.file.click()"
        class="relative mt-8 grid min-h-72 cursor-pointer place-items-center rounded-[24px] border border-dashed p-8 text-center transition duration-300"
        :class="dragging ? 'border-white/35 bg-white/[0.07]' : 'border-white/[0.10] bg-black/25 hover:bg-white/[0.035]'">
        <input x-ref="file" type="file" class="hidden" accept=".ogg,.mp3,.wav,.m4a,audio/*" @change="handleFile($event.target.files[0])">
        <div>
            <div class="mx-auto mb-4 grid size-14 place-items-center rounded-[18px] border border-white/[0.08] bg-white/[0.035] text-white/80">
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M20 16v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3"/></svg>
            </div>
            <p class="font-semibold">Upload Audio File</p>
            <p class="mt-2 text-sm text-[#A3A3A3]">Drop MP3, WAV, M4A, or OGG here</p>
        </div>
    </div>

    <div x-show="fileName" class="relative mt-6 space-y-4">
        <div class="flex items-center justify-between rounded-[20px] border border-white/[0.06] bg-black/25 p-4">
            <div>
                <p x-text="fileName" class="font-medium"></p>
                <p x-text="fileSize" class="text-sm text-[#A3A3A3]"></p>
            </div>
            <button type="button" x-show="uploading" @click="cancelUpload" class="rounded-full border border-rose-400/25 px-4 py-2 text-sm text-rose-200">Cancel Upload</button>
        </div>

        <div>
            <div class="mb-2 flex justify-between text-sm text-[#A3A3A3]"><span x-text="statusLabel"></span><span x-text="progress + '%'"></span></div>
            <div class="h-2 rounded-full" style="background: color-mix(in srgb, var(--foreground) 10%, transparent);"><div class="h-2 rounded-full transition-all duration-300" :style="`width: ${progress}%; background: var(--foreground);`"></div></div>
            <p class="mt-2 text-xs text-[#6B7280]">Estimated time: under 10 seconds for most files</p>
        </div>

        <div class="grid gap-2 sm:grid-cols-5">
            <template x-for="step in timeline" :key="step">
                <div class="rounded-full border px-3 py-2 text-center text-sm" :class="isStepActive(step) ? 'border-white/20 bg-white/[0.06] text-white' : 'border-white/10 text-[#6B7280]'" x-text="step"></div>
            </template>
        </div>
    </div>

    <div x-show="result" class="relative mt-6 rounded-[24px] border border-white/[0.08] bg-[#151618] p-5">
        <h3 class="mb-4 font-semibold">Result</h3>
        <div class="mb-4 rounded-[18px] border border-white/[0.05] bg-black/20 p-4">
            <p class="text-sm text-[#A3A3A3]">Current Balance</p>
            <p class="mt-1 font-semibold"><span x-text="creditBalance"></span> Credits</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
            <template x-for="item in resultItems()" :key="item.label">
                <div class="rounded-[18px] border border-white/[0.05] bg-black/20 p-4">
                    <p class="text-sm text-[#A3A3A3]" x-text="item.label"></p>
                    <p class="mt-1 font-medium" x-text="item.value"></p>
                </div>
            </template>
        </div>
        <div class="mt-5 grid gap-3 sm:grid-cols-2">
            <div class="rounded-[18px] border border-white/[0.05] bg-black/20 p-4">
                <p class="text-sm text-[#A3A3A3]">{{ __('converter.file_count') }}</p>
                <p class="mt-1 font-medium" x-text="result?.split_count || 1"></p>
            </div>
            <div class="rounded-[18px] border border-white/[0.05] bg-black/20 p-4">
                <p class="text-sm text-[#A3A3A3]">{{ __('converter.total_duration') }}</p>
                <p class="mt-1 font-medium" x-text="formatDuration(result?.total_duration)"></p>
            </div>
        </div>

        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-3">
            <button x-show="(result?.split_count || 1) > 1" type="button" @click="downloadAllFiles" :disabled="downloadingAll" class="wx-btn-primary w-full px-4 py-2.5 text-sm sm:w-auto" x-text="downloadingAll ? 'Preparing...' : `{{ __('converter.download_all') }}`"></button>
            @auth
                @if(auth()->user()->robloxAccount)
                    <button x-show="(result?.split_count || 1) > 1" type="button" @click="uploadAllToRoblox" :disabled="uploadingAllRoblox" class="wx-btn-secondary w-full px-4 py-2.5 text-sm sm:w-auto" x-text="uploadingAllRoblox ? '{{ __('converter.uploading') }}' : `{{ __('converter.upload_all_roblox') }}`"></button>
                @endif
            @endauth
            <button type="button" @click="reset" class="wx-btn-secondary w-full px-4 py-2.5 text-sm sm:w-auto">Convert Again</button>
        </div>

        @auth
            @unless(auth()->user()->robloxAccount)
                <p class="mt-3 text-sm text-[#A3A3A3]">Please connect your Roblox account to upload files. <a href="{{ route('roblox.connect') }}" class="text-white underline">Connect Roblox</a></p>
            @endunless
        @endauth

        {{-- Desktop: table --}}
        <div class="mt-5 hidden overflow-hidden rounded-[18px] border border-white/[0.05] lg:block">
            <table class="min-w-full text-sm">
                <thead class="border-b border-white/[0.06] bg-black/20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.12em] text-[#6B7280]">{{ __('converter.file_name') }}</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.12em] text-[#6B7280]">Description</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.12em] text-[#6B7280]">Duration</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.12em] text-[#6B7280]">Status</th>
                        <th class="px-4 py-3 text-left text-xs uppercase tracking-[0.12em] text-[#6B7280]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.06]">
                    <template x-for="file in (result?.files || [])" :key="file.id">
                        <tr>
                            <td class="px-4 py-3 font-medium" x-text="file.label"></td>
                            <td class="px-4 py-3 text-[#A3A3A3]" x-text="file.roblox_description || 'UPLOAD FROM NPNH CREATIVE'"></td>
                            <td class="px-4 py-3 text-[#A3A3A3]" x-text="formatDuration(file.duration)"></td>
                            <td class="px-4 py-3 text-[#A3A3A3]" x-text="fileState(file).message || (file.upload_status === 'uploaded' ? '{{ __('converter.uploaded') }}' : '')"></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="downloadFile(file)" :disabled="fileState(file).downloading" class="wx-btn-secondary px-3 py-1.5 text-xs" x-text="fileState(file).downloading ? '...' : '{{ __('converter.download') }}'"></button>
                                    @auth
                                        @if(auth()->user()->robloxAccount)
                                            <button type="button" @click="uploadFileToRoblox(file)" :disabled="fileState(file).uploading" class="wx-btn-secondary px-3 py-1.5 text-xs" x-text="fileState(file).uploading ? '{{ __('converter.uploading') }}' : '{{ __('converter.upload_roblox') }}'"></button>
                                        @endif
                                    @endauth
                                    <a x-show="file.roblox_creator_url" :href="file.roblox_creator_url" target="_blank" rel="noopener" class="wx-btn-secondary px-3 py-1.5 text-xs">Creator Hub</a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile: cards --}}
        <div class="mt-5 grid gap-3 lg:hidden">
            <template x-for="file in (result?.files || [])" :key="file.id">
                <div class="rounded-[18px] border border-white/[0.05] bg-black/20 p-4">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-medium" x-text="file.label"></p>
                        <p class="shrink-0 text-sm text-[#A3A3A3]" x-text="formatDuration(file.duration)"></p>
                    </div>
                    <p x-show="fileState(file).message || file.upload_status === 'uploaded'" class="mt-2 text-xs text-[#A3A3A3]" x-text="fileState(file).message || '{{ __('converter.uploaded') }}'"></p>
                    <p class="mt-2 text-xs uppercase tracking-[0.14em] text-[#A3A3A3]" x-text="file.roblox_description || 'UPLOAD FROM NPNH CREATIVE'"></p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button type="button" @click="downloadFile(file)" :disabled="fileState(file).downloading" class="wx-btn-secondary flex-1 px-3 py-2 text-xs" x-text="fileState(file).downloading ? '...' : '{{ __('converter.download') }}'"></button>
                        @auth
                            @if(auth()->user()->robloxAccount)
                                <button type="button" @click="uploadFileToRoblox(file)" :disabled="fileState(file).uploading" class="wx-btn-secondary flex-1 px-3 py-2 text-xs" x-text="fileState(file).uploading ? '{{ __('converter.uploading') }}' : '{{ __('converter.upload_roblox') }}'"></button>
                            @endif
                        @endauth
                    </div>
                    <a x-show="file.roblox_creator_url" :href="file.roblox_creator_url" target="_blank" rel="noopener" class="mt-2 inline-block text-xs text-[#A3A3A3] underline">Open Creator Hub</a>
                </div>
            </template>
        </div>
    </div>

    {{-- Upgrade Plan Modal --}}
    <div x-show="showUpgradeModal" x-cloak class="fixed inset-0 z-[999999] grid place-items-center bg-black/60 p-4" @click.self="showUpgradeModal = false" @keydown.escape.window="showUpgradeModal = false">
        <div class="wx-card w-full max-w-sm p-6 text-center" @click.stop>
            <h3 class="text-lg font-semibold text-white">{{ __('converter.insufficient_credits_title') }}</h3>
            <p class="mt-2 text-sm text-[#A3A3A3]" x-text="upgradeMessage || `{{ __('converter.insufficient_credits_body') }}`"></p>
            <div class="mt-5 flex justify-center gap-2">
                <button type="button" @click="showUpgradeModal = false" class="wx-btn-secondary px-5 py-2.5 text-sm">Close</button>
                <a href="{{ route('app.pricing') }}" class="wx-btn-primary px-5 py-2.5 text-sm">{{ __('converter.upgrade_plan') }}</a>
            </div>
        </div>
    </div>
</div>
