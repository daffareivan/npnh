<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ConversionStatus;
use App\Models\AudioFile;
use App\Models\ConversionJob;
use App\Repositories\AudioFileRepository;
use App\Services\AudioMetadataService;
use App\Services\FfmpegAudioConverter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessAudioConversion implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(public readonly int $audioFileId) {}

    public function handle(
        AudioFileRepository $audioFiles,
        AudioMetadataService $metadata,
        FfmpegAudioConverter $converter,
    ): void {
        $audioFile = AudioFile::query()->findOrFail($this->audioFileId);
        $jobRecord = ConversionJob::query()->where('audio_file_id', $audioFile->id)->latest()->first();

        $jobRecord?->forceFill(['status' => ConversionStatus::Analyzing, 'started_at' => now(), 'attempts' => $this->attempts()])->save();
        $audioFiles->updateStatus($audioFile, ConversionStatus::Analyzing);

        $inputPath = Storage::path($audioFile->original_path);
        $duration = $metadata->duration($inputPath);

        $audioFile->forceFill(['duration' => $duration])->save();
        $audioFiles->updateStatus($audioFile, ConversionStatus::Converting);

        $outputPath = config('converter.storage_path').'/outputs/'.pathinfo($audioFile->original_path, PATHINFO_FILENAME).'.ogg';
        Storage::makeDirectory(dirname($outputPath));

        $audioFiles->updateStatus($audioFile, ConversionStatus::Encoding);
        $converter->convert($inputPath, Storage::path($outputPath), (float) $audioFile->speed, $audioFile->amplify_db);

        $audioFile->forceFill([
            'output_path' => $outputPath,
            'output_size' => Storage::size($outputPath),
            'status' => ConversionStatus::Finished,
            'progress' => ConversionStatus::Finished->progress(),
            'finished_at' => now(),
        ])->save();

        $jobRecord?->forceFill(['status' => ConversionStatus::Finished, 'finished_at' => now()])->save();
    }

    public function failed(Throwable $exception): void
    {
        $audioFile = AudioFile::query()->find($this->audioFileId);

        if (! $audioFile) {
            return;
        }

        $message = substr($exception->getMessage(), 0, 1000);
        $audioFile->forceFill(['status' => ConversionStatus::Failed, 'progress' => 0, 'error_message' => $message])->save();
        ConversionJob::query()->where('audio_file_id', $audioFile->id)->latest()->first()
            ?->forceFill(['status' => ConversionStatus::Failed, 'finished_at' => now(), 'error_message' => $message])->save();
    }
}
