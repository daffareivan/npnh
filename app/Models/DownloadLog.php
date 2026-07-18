<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadLog extends Model
{
    protected $fillable = ['audio_file_id', 'conversion_file_id', 'user_id', 'ip_address', 'user_agent'];

    public function audioFile(): BelongsTo
    {
        return $this->belongsTo(AudioFile::class);
    }

    public function conversionFile(): BelongsTo
    {
        return $this->belongsTo(ConversionFile::class);
    }
}
