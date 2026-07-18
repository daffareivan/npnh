<?php

declare(strict_types=1);

namespace App\Http\Controllers\Converter;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversionFileResource;
use App\Models\ActivityLog;
use App\Models\AudioFile;
use App\Models\ConversionFile;
use App\Models\DownloadLog;
use App\Services\CreditService;
use App\Services\Roblox\RobloxAssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use ZipArchive;

class ConversionFileController extends Controller
{
    public function download(ConversionFile $conversionFile, Request $request, CreditService $credits): StreamedResponse
    {
        $this->authorize('download', $conversionFile);

        abort_unless($conversionFile->file_path && Storage::exists($conversionFile->file_path), 404);

        $user = $request->user();
        $cost = $credits->get(CreditService::DOWNLOAD_COST);

        abort_unless(
            $credits->hasEnoughCredits($user, $cost),
            402,
            "Insufficient Credits\n\nYou don't have enough credits to perform this action.\n\nPlease purchase additional credits or contact the administrator."
        );

        $credits->deduct(
            user: $user,
            cost: $cost,
            action: 'Download Audio',
            subject: $conversionFile,
            metadata: ['file' => $conversionFile->file_name, 'sequence' => $conversionFile->sequence],
        );

        DownloadLog::query()->create([
            'audio_file_id' => $conversionFile->audio_file_id,
            'conversion_file_id' => $conversionFile->id,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $conversionFile->forceFill(['downloaded_at' => now()])->save();

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'subject_type' => $conversionFile::class,
            'subject_id' => $conversionFile->id,
            'event' => 'download',
            'properties' => ['file' => $conversionFile->file_name],
        ]);

        $response = Storage::download($conversionFile->file_path, $conversionFile->file_name);
        $response->headers->set('X-Credit-Balance', (string) $user->refresh()->credits_balance);

        return $response;
    }

    public function downloadAll(AudioFile $audioFile, Request $request, CreditService $credits): Response|RedirectResponse
    {
        $this->authorize('view', $audioFile);

        $files = $audioFile->files()->get();
        abort_if($files->isEmpty(), 404);

        $user = $request->user();
        $cost = $credits->get(CreditService::DOWNLOAD_COST) * $files->count();

        abort_unless(
            $credits->hasEnoughCredits($user, $cost),
            402,
            "Insufficient Credits\n\nYou don't have enough credits to download all files.\n\nPlease purchase additional credits or contact the administrator."
        );

        $zipRelativePath = config('converter.storage_path').'/zips/'.$audioFile->id.'-'.now()->timestamp.'.zip';
        Storage::makeDirectory(dirname($zipRelativePath));
        $zipAbsolutePath = Storage::path($zipRelativePath);

        $zip = new ZipArchive();
        abort_unless($zip->open($zipAbsolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true, 500, 'Could not create archive.');

        foreach ($files as $file) {
            if (Storage::exists($file->file_path)) {
                $zip->addFile(Storage::path($file->file_path), $file->file_name);
            }
        }

        $zip->close();

        $credits->deduct(
            user: $user,
            cost: $cost,
            action: 'Download All Audio',
            subject: $audioFile,
            metadata: ['file_count' => $files->count()],
        );

        foreach ($files as $file) {
            DownloadLog::query()->create([
                'audio_file_id' => $audioFile->id,
                'conversion_file_id' => $file->id,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
            $file->forceFill(['downloaded_at' => now()])->save();
        }

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'subject_type' => $audioFile::class,
            'subject_id' => $audioFile->id,
            'event' => 'download_all',
            'properties' => ['file' => $audioFile->original_name, 'file_count' => $files->count()],
        ]);

        $downloadName = pathinfo((string) $audioFile->original_name, PATHINFO_FILENAME).'.zip';

        return response()->download($zipAbsolutePath, $downloadName, [
            'X-Credit-Balance' => (string) $user->refresh()->credits_balance,
        ])->deleteFileAfterSend(true);
    }

    public function uploadRoblox(ConversionFile $conversionFile, Request $request, RobloxAssetService $assets, CreditService $credits): ConversionFileResource
    {
        $this->authorize('download', $conversionFile);

        $user = $request->user();
        $account = $user->robloxAccount;

        abort_unless($account, 422, 'Please connect your Roblox account first.');

        $cost = $credits->get(CreditService::ROBLOX_UPLOAD_COST);

        abort_unless(
            $credits->hasEnoughCredits($user, $cost),
            402,
            "Insufficient Credits\n\nYou don't have enough credits to perform this action.\n\nPlease purchase additional credits or contact the administrator."
        );

        $credits->deduct($user, $cost, 'Upload Roblox', $conversionFile, ['file' => $conversionFile->file_name]);

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'subject_type' => $conversionFile::class,
            'subject_id' => $conversionFile->id,
            'event' => 'Roblox Upload Started',
            'properties' => ['file' => $conversionFile->file_name],
        ]);

        try {
            $assets->uploadConversionFile($conversionFile, $account);

            $conversionFile->forceFill(['uploaded_at' => now()])->save();

            ActivityLog::query()->create([
                'user_id' => $user->id,
                'subject_type' => $conversionFile::class,
                'subject_id' => $conversionFile->id,
                'event' => 'Roblox Upload Completed',
                'properties' => ['asset_id' => $conversionFile->refresh()->roblox_asset_id],
            ]);
        } catch (Throwable $exception) {
            if ($credits->get(CreditService::REFUND_FAILED_UPLOAD)) {
                $credits->refund($user, $cost, 'Refund Upload', $conversionFile, ['reason' => $exception->getMessage()]);
            }

            $conversionFile->forceFill([
                'roblox_status' => 'failed',
                'roblox_error_message' => $exception->getMessage(),
                'roblox_creator_url' => config('services.roblox.creator_hub_url'),
            ])->save();

            ActivityLog::query()->create([
                'user_id' => $user->id,
                'subject_type' => $conversionFile::class,
                'subject_id' => $conversionFile->id,
                'event' => 'Roblox Upload Failed',
                'properties' => ['message' => $exception->getMessage()],
            ]);

            abort(422, $exception->getMessage());
        }

        return ConversionFileResource::make($conversionFile->refresh());
    }
}
