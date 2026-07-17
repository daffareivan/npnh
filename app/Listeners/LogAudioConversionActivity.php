<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\AudioConversionStatusChanged;
use App\Models\ActivityLog;

class LogAudioConversionActivity
{
    public function handle(AudioConversionStatusChanged $event): void
    {
        ActivityLog::query()->create([
            'user_id' => $event->audioFile->user_id,
            'subject_type' => $event->audioFile::class,
            'subject_id' => $event->audioFile->id,
            'event' => 'conversion.status.changed',
            'properties' => ['status' => $event->audioFile->status->value],
        ]);
    }
}
