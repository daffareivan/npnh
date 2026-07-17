<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\AudioFileResource;
use App\Models\ActivityLog;
use App\Models\AudioFile;
use App\Services\CreditService;
use App\Services\Roblox\RobloxAssetService;
use Illuminate\Http\Request;
use Throwable;

class RobloxAssetController extends Controller
{
    public function upload(Request $request, RobloxAssetService $assets, CreditService $credits): AudioFileResource
    {
        $request->validate([
            'audio_file_id' => ['required', 'integer', 'exists:audio_files,id'],
        ]);

        $audioFile = AudioFile::query()->findOrFail($request->integer('audio_file_id'));
        $this->authorize('view', $audioFile);

        $account = $request->user()?->robloxAccount;

        abort_unless($account, 422, 'Please connect your Roblox account first.');

        $cost = $credits->get(CreditService::ROBLOX_UPLOAD_COST);
        abort_unless(
            $credits->hasEnoughCredits($request->user(), $cost),
            402,
            "Insufficient Credits\n\nYou don't have enough credits to perform this action.\n\nPlease purchase additional credits or contact the administrator."
        );

        $credits->deduct($request->user(), $cost, 'Upload Roblox', $audioFile, ['file' => $audioFile->original_name]);

        ActivityLog::query()->create([
            'user_id' => $request->user()->id,
            'subject_type' => $audioFile::class,
            'subject_id' => $audioFile->id,
            'event' => 'Roblox Upload Started',
            'properties' => ['file' => $audioFile->original_name],
        ]);

        try {
            $assets->uploadAudio($audioFile, $account);

            ActivityLog::query()->create([
                'user_id' => $request->user()->id,
                'subject_type' => $audioFile::class,
                'subject_id' => $audioFile->id,
                'event' => 'Roblox Upload Completed',
                'properties' => ['asset_id' => $audioFile->refresh()->roblox_asset_id],
            ]);
        } catch (Throwable $exception) {
            if ($credits->get(CreditService::REFUND_FAILED_UPLOAD)) {
                $credits->refund($request->user(), $cost, 'Refund Upload', $audioFile, ['reason' => $exception->getMessage()]);
            }

            $audioFile->forceFill([
                'roblox_status' => 'failed',
                'roblox_error_message' => $exception->getMessage(),
                'roblox_creator_url' => config('services.roblox.creator_hub_url'),
            ])->save();

            ActivityLog::query()->create([
                'user_id' => $request->user()->id,
                'subject_type' => $audioFile::class,
                'subject_id' => $audioFile->id,
                'event' => 'Roblox Upload Failed',
                'properties' => ['message' => $exception->getMessage()],
            ]);

            abort(422, $exception->getMessage());
        }

        $request->user()->refresh();

        return AudioFileResource::make($audioFile->refresh());
    }
}
