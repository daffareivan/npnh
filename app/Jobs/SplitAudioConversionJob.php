<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\AudioFile;
use App\Notifications\ConversionCompletedNotification;
use App\Services\AudioSplitService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Runs after ProcessAudioConversion finishes encoding: Split -> Generate Waveform
 * -> Save Database -> Notification. Kept as a single queued job since these
 * steps are tightly sequential and share the same ConversionFile records.
 */
class SplitAudioConversionJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(public readonly int $audioFileId) {}

    public function handle(AudioSplitService $splitter): void
    {
        $audioFile = AudioFile::query()->findOrFail($this->audioFileId);

        if (! $audioFile->output_path || ! Storage::exists($audioFile->output_path)) {
            return;
        }

        // Split
        $files = $splitter->split($audioFile);

        // Generate Waveform
        foreach ($files as $file) {
            $peaks = $splitter->generatePeaks(Storage::path($file->file_path));

            if ($peaks !== []) {
                $file->forceFill(['waveform_peaks' => $peaks])->save();
            }
        }

        // Save Database is already done inside AudioSplitService::split().

        // Notification
        $audioFile->user?->notify(new ConversionCompletedNotification($audioFile, $files->count()));

        ActivityLog::query()->create([
            'user_id' => $audioFile->user_id,
            'subject_type' => $audioFile::class,
            'subject_id' => $audioFile->id,
            'event' => $files->count() > 1 ? 'split' : 'convert',
            'properties' => [
                'file' => $audioFile->original_name,
                'file_count' => $files->count(),
                'total_duration' => (float) $files->sum('duration'),
            ],
        ]);
    }

    public function failed(Throwable $exception): void
    {
        ActivityLog::query()->create([
            'user_id' => AudioFile::query()->find($this->audioFileId)?->user_id,
            'subject_type' => AudioFile::class,
            'subject_id' => $this->audioFileId,
            'event' => 'split_failed',
            'properties' => ['message' => substr($exception->getMessage(), 0, 500)],
        ]);
    }
}
