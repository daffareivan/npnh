<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ConversionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AudioFile extends Model
{
    protected $fillable = [
        'user_id',
        'conversion_preset_id',
        'original_name',
        'mime_type',
        'extension',
        'original_path',
        'output_path',
        'original_size',
        'output_size',
        'duration',
        'speed',
        'amplify_db',
        'status',
        'roblox_status',
        'roblox_asset_id',
        'roblox_creator_url',
        'roblox_error_message',
        'progress',
        'error_message',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ConversionStatus::class,
            'original_size' => 'integer',
            'output_size' => 'integer',
            'duration' => 'decimal:3',
            'speed' => 'decimal:1',
            'amplify_db' => 'integer',
            'progress' => 'integer',
            'finished_at' => 'datetime',
        ];
    }

    public function preset(): BelongsTo
    {
        return $this->belongsTo(ConversionPreset::class, 'conversion_preset_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversionJobs(): HasMany
    {
        return $this->hasMany(ConversionJob::class);
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ConversionFile::class)->orderBy('sequence');
    }

    public function isSplit(): bool
    {
        return $this->files()->count() > 1;
    }
}
