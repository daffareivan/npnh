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
        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-3">
            <button x-show="creditBalance >= downloadCost" type="button" @click="downloadResult" :disabled="downloading" class="wx-btn-primary w-full px-4 py-2.5 text-sm sm:w-auto" x-text="downloading ? 'Downloading...' : `Download (-${downloadCost} Credit)`"></button>
            <button x-show="creditBalance < downloadCost" type="button" disabled class="w-full rounded-full border border-white/10 bg-white/[0.03] px-4 py-2.5 text-sm text-[#6B7280] sm:w-auto">Download - Insufficient Credits</button>
            <button type="button" @click="reset" class="wx-btn-secondary w-full px-4 py-2.5 text-sm sm:w-auto">Convert Again</button>
        </div>
        <p x-show="downloadMessage" class="mt-3 text-sm text-[#A3A3A3]" x-text="downloadMessage"></p>
        @auth
            <div class="mt-5 rounded-[20px] border border-white/[0.06] bg-black/20 p-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold">Upload to Roblox</p>
                        @if(auth()->user()->robloxAccount)
                            <p class="mt-1 text-sm text-[#A3A3A3]">Upload the converted audio to Roblox using the official Open Cloud Assets API.</p>
                            <p class="mt-1 text-sm text-[#A3A3A3]">Cost: <span x-text="robloxUploadCost"></span> Credits</p>
                            <p x-show="robloxMessage" class="mt-2 text-sm text-white" x-text="robloxMessage"></p>
                            <p x-show="result?.roblox_status === 'uploaded'" class="mt-2 text-sm text-white">Uploaded to Roblox Creator Hub.</p>
                            <p x-show="result?.roblox_status === 'processing'" class="mt-2 text-sm text-[#A3A3A3]">Roblox has accepted the upload and is processing/moderating the asset. It can appear in Creator Hub before the API returns the final Asset ID.</p>
                            <template x-if="result?.roblox_asset_id">
                                <p class="mt-2 text-sm text-[#A3A3A3]">Asset ID: <span class="text-white" x-text="result.roblox_asset_id"></span></p>
                            </template>
                        @else
                            <p class="mt-1 text-sm text-[#A3A3A3]">Please connect your Roblox account first.</p>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                        @if(auth()->user()->robloxAccount)
                            <button x-show="creditBalance >= robloxUploadCost" type="button" @click="uploadToRoblox" class="wx-btn-primary w-full px-4 py-2.5 text-sm sm:w-auto" :disabled="robloxUploading" x-text="robloxUploading ? 'Uploading...' : `Upload to Roblox (-${robloxUploadCost} Credits)`"></button>
                            <button x-show="creditBalance < robloxUploadCost" type="button" disabled class="w-full rounded-full border border-white/10 bg-white/[0.03] px-4 py-2.5 text-sm text-[#6B7280] sm:w-auto">Upload - Insufficient Credits</button>
                            <a x-show="result?.roblox_creator_url" :href="result?.roblox_creator_url" target="_blank" rel="noopener" class="wx-btn-secondary w-full px-4 py-2.5 text-sm sm:w-auto">Open Creator Hub</a>
                            <button x-show="result?.id" type="button" @click="refreshResultStatus" class="wx-btn-secondary w-full px-4 py-2.5 text-sm sm:w-auto">Refresh Status</button>
                            <button x-show="result?.roblox_asset_id" type="button" @click="navigator.clipboard.writeText(result.roblox_asset_id)" class="wx-btn-secondary w-full px-4 py-2.5 text-sm sm:w-auto">Copy Asset ID</button>
                        @else
                            <a href="{{ route('roblox.connect') }}" class="wx-btn-primary w-full px-4 py-2.5 text-sm sm:w-auto">Connect Roblox</a>
                        @endif
                    </div>
                </div>
            </div>
        @endauth
    </div>
</div>
