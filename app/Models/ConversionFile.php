<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConversionFile extends Model
{
    protected $fillable = [
        'audio_file_id',
        'sequence',
        'file_name',
        'file_path',
        'duration',
        'size',
        'status',
        'waveform_peaks',
        'roblox_status',
        'roblox_asset_id',
        'roblox_creator_url',
        'roblox_error_message',
        'downloaded_at',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'duration' => 'decimal:3',
            'size' => 'integer',
            'waveform_peaks' => 'array',
            'downloaded_at' => 'datetime',
            'uploaded_at' => 'datetime',
        ];
    }

    public function audioFile(): BelongsTo
    {
        return $this->belongsTo(AudioFile::class);
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function label(): string
    {
        return 'File '.$this->sequence;
    }
}
