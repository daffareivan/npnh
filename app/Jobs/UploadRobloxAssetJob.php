<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AudioFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UploadRobloxAssetJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $audioFileId) {}

    public function handle(): void
    {
        $audioFile = AudioFile::query()->findOrFail($this->audioFileId);
        $audioFile->forceFill([
            'roblox_status' => 'pending',
            'roblox_creator_url' => config('services.roblox.creator_hub_url'),
            'roblox_error_message' => 'Automatic upload is currently unavailable through the official Roblox API.',
        ])->save();
    }
}
