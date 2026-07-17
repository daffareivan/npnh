<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class AudioFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->original_name,
            'original_size' => $this->original_size,
            'output_size' => $this->output_size,
            'duration' => $this->duration,
            'speed' => (float) $this->speed,
            'amplify_db' => $this->amplify_db,
            'status' => $this->status->value,
            'progress' => $this->progress,
            'error_message' => $this->error_message,
            'roblox_status' => $this->roblox_status,
            'roblox_asset_id' => $this->roblox_asset_id,
            'roblox_creator_url' => $this->roblox_creator_url,
            'roblox_error_message' => $this->roblox_error_message,
            'created_at' => $this->created_at?->toISOString(),
            'finished_at' => $this->finished_at?->toISOString(),
            'download_url' => $this->output_path ? URL::temporarySignedRoute('api.converter.download', now()->addMinutes(15), $this->resource) : null,
            'credit_balance' => $request->user()?->credits_balance,
        ];
    }
}
