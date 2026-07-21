<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ConversionFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sequence' => $this->sequence,
            'label' => $this->label(),
            'file_name' => $this->file_name,
            'duration' => $this->duration ? (float) $this->duration : null,
            'size' => $this->size,
            'status' => $this->status,
            'upload_status' => $this->roblox_status,
            'downloaded_at' => $this->downloaded_at?->toISOString(),
            'uploaded_at' => $this->uploaded_at?->toISOString(),
            'roblox_status' => $this->roblox_status,
            'roblox_asset_id' => $this->roblox_asset_id,
            'roblox_creator_url' => $this->roblox_creator_url,
            'roblox_error_message' => $this->roblox_error_message,
            'roblox_description' => 'UPLOAD FROM NPNH CREATIVE',
            'download_url' => URL::temporarySignedRoute('api.converter.files.download', now()->addMinutes(15), $this->resource),
        ];
    }
}
