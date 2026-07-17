<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ConversionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversionJob extends Model
{
    protected $fillable = ['audio_file_id', 'queue_name', 'status', 'attempts', 'started_at', 'finished_at', 'error_message'];

    protected function casts(): array
    {
        return [
            'status' => ConversionStatus::class,
            'attempts' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function audioFile(): BelongsTo
    {
        return $this->belongsTo(AudioFile::class);
    }
}
