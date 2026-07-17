<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ConverterUploadData;
use App\Enums\ConversionStatus;
use App\Jobs\ProcessAudioConversion;
use App\Models\ActivityLog;
use App\Models\AudioFile;
use App\Models\ConversionJob;
use App\Models\ConversionPreset;
use App\Models\DownloadLog;
use App\Repositories\AudioFileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConverterService
{
    public function __construct(private readonly AudioFileRepository $audioFiles) {}

    public function upload(ConverterUploadData $data): AudioFile
    {
        $preset = ConversionPreset::query()->findOrFail($data->presetId);
        $path = $data->file->store(config('converter.storage_path').'/originals');

        $audioFile = $this->audioFiles->create([
            'user_id' => $data->userId,
            'conversion_preset_id' => $preset->id,
            'original_name' => $data->file->getClientOriginalName(),
            'mime_type' => $data->file->getMimeType() ?? 'application/octet-stream',
            'extension' => $data->file->getClientOriginalExtension(),
            'original_path' => $path,
            'original_size' => $data->file->getSize(),
            'speed' => $preset->speed,
            'amplify_db' => $preset->amplify_db,
            'status' => ConversionStatus::Uploaded,
            'progress' => ConversionStatus::Uploaded->progress(),
        ]);

        ActivityLog::query()->create([
            'user_id' => $data->userId,
            'subject_type' => $audioFile::class,
            'subject_id' => $audioFile->id,
            'event' => 'upload',
            'properties' => ['file' => $audioFile->original_name],
        ]);

        return $audioFile;
    }

    public function dispatch(AudioFile $audioFile): AudioFile
    {
        ConversionJob::query()->create([
            'audio_file_id' => $audioFile->id,
            'queue_name' => config('converter.queue'),
            'status' => ConversionStatus::Pending,
        ]);

        ProcessAudioConversion::dispatch($audioFile->id)->onQueue(config('converter.queue'));

        return $this->audioFiles->updateStatus($audioFile, ConversionStatus::Pending);
    }

    public function download(AudioFile $audioFile, Request $request): StreamedResponse
    {
        abort_unless($audioFile->status === ConversionStatus::Finished && $audioFile->output_path, 404);

        $creditService = app(CreditService::class);
        $downloadCost = $creditService->get(CreditService::DOWNLOAD_COST);

        abort_unless(
            $creditService->hasEnoughCredits($request->user(), $downloadCost),
            402,
            "Insufficient Credits\n\nYou don't have enough credits to perform this action.\n\nPlease purchase additional credits or contact the administrator."
        );

        $creditService->deduct(
            user: $request->user(),
            cost: $downloadCost,
            action: 'Download Audio',
            subject: $audioFile,
            metadata: ['file' => $audioFile->original_name],
        );

        DownloadLog::query()->create([
            'audio_file_id' => $audioFile->id,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        ActivityLog::query()->create([
            'user_id' => $request->user()?->id,
            'subject_type' => $audioFile::class,
            'subject_id' => $audioFile->id,
            'event' => 'download',
            'properties' => ['file' => $audioFile->original_name],
        ]);

        $response = Storage::download($audioFile->output_path, pathinfo($audioFile->original_name, PATHINFO_FILENAME).'.ogg');
        $response->headers->set('X-Credit-Balance', (string) $request->user()->refresh()->credits_balance);

        return $response;
    }

    public function delete(AudioFile $audioFile): void
    {
        Storage::delete(array_filter([$audioFile->original_path, $audioFile->output_path]));
        ActivityLog::query()->create([
            'user_id' => auth()->id(),
            'subject_type' => $audioFile::class,
            'subject_id' => $audioFile->id,
            'event' => 'delete',
            'properties' => ['file' => $audioFile->original_name],
        ]);
        $audioFile->forceFill(['status' => ConversionStatus::Deleted])->save();
        $audioFile->delete();
    }
}
