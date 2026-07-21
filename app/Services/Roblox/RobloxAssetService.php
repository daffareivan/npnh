<?php

declare(strict_types=1);

namespace App\Services\Roblox;

use App\DTO\Roblox\AssetDTO;
use App\Models\AudioFile;
use App\Models\ConversionFile;
use App\Models\RobloxAccount;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class RobloxAssetService
{
    public function __construct(
        private readonly RobloxOAuthService $oauth,
    ) {}

    public function uploadAudio(AudioFile $audioFile, RobloxAccount $account): AssetDTO
    {
        if (! $audioFile->output_path || ! Storage::exists($audioFile->output_path)) {
            throw new RuntimeException('Converted file is not available yet.');
        }

        $audioFile->forceFill(['roblox_status' => 'uploading', 'roblox_error_message' => null])->save();

        $asset = $this->performUpload(
            Storage::path($audioFile->output_path),
            pathinfo($audioFile->original_name, PATHINFO_FILENAME),
            $account,
        );

        $audioFile->forceFill([
            'roblox_status' => $asset->assetId ? 'uploaded' : $asset->status,
            'roblox_asset_id' => $asset->assetId,
            'roblox_creator_url' => $asset->creatorUrl,
            'roblox_error_message' => $asset->message,
        ])->save();

        return $asset;
    }

    public function uploadConversionFile(ConversionFile $conversionFile, RobloxAccount $account): AssetDTO
    {
        if (! $conversionFile->file_path || ! Storage::exists($conversionFile->file_path)) {
            throw new RuntimeException('Converted file is not available yet.');
        }

        $conversionFile->forceFill(['roblox_status' => 'uploading', 'roblox_error_message' => null])->save();

        $asset = $this->performUpload(
            Storage::path($conversionFile->file_path),
            pathinfo($conversionFile->file_name, PATHINFO_FILENAME),
            $account,
        );

        $conversionFile->forceFill([
            'roblox_status' => $asset->assetId ? 'uploaded' : $asset->status,
            'roblox_asset_id' => $asset->assetId,
            'roblox_creator_url' => $asset->creatorUrl,
            'roblox_error_message' => $asset->message,
        ])->save();

        return $asset;
    }

    private function performUpload(string $path, string $displayName, RobloxAccount $account): AssetDTO
    {
        $request = [
            'assetType' => 'Audio',
            'displayName' => mb_substr($displayName, 0, 50),
            'description' => 'UPLOAD FROM NPNH CREATIVE',
            'creationContext' => [
                'creator' => $this->creatorContext($account),
            ],
        ];

        $response = $this->sendUploadRequest($path, $request, $account);

        $operationPath = $response['path'] ?? null;

        if (! $operationPath) {
            throw new RuntimeException('Roblox did not return an upload operation.');
        }

        return $this->waitForOperation($operationPath, $account);
    }

    public function prepareManualUpload(AudioFile $audioFile): AssetDTO
    {
        return new AssetDTO(
            assetId: null,
            status: 'unavailable',
            creatorUrl: config('services.roblox.creator_hub_url', 'https://create.roblox.com/dashboard/creations'),
            message: 'Automatic upload is currently unavailable through the official Roblox API. Please download the converted file and upload it manually using Roblox Creator Hub.',
        );
    }

    private function waitForOperation(string $operationPath, RobloxAccount $account): AssetDTO
    {
        $operationId = basename($operationPath);

        for ($attempt = 0; $attempt < 30; $attempt++) {
            $operation = $this->getOperation($operationId, $account);

            if (($operation['done'] ?? false) === true) {
                $assetId = $operation['response']['assetId'] ?? $operation['response']['asset']['assetId'] ?? $operation['response']['asset']['id'] ?? null;
                $status = $operation['error']['message'] ?? null;

                return new AssetDTO(
                    assetId: $assetId ? (string) $assetId : null,
                    status: $assetId ? 'uploaded' : 'failed',
                    creatorUrl: $assetId ? "https://create.roblox.com/dashboard/creations/store/{$assetId}/configure" : config('services.roblox.creator_hub_url'),
                    message: $status,
                );
            }

            sleep(1);
        }

        return new AssetDTO(
            assetId: null,
            status: 'processing',
            creatorUrl: config('services.roblox.creator_hub_url'),
            message: 'Roblox is still processing this asset. Check Creator Hub in a moment.',
        );
    }

    private function assetClient(RobloxAccount $account): PendingRequest
    {
        if (config('services.roblox.upload_auth', 'oauth') === 'oauth') {
            return Http::withToken($this->oauth->accessTokenFor($account))->acceptJson();
        }

        $apiKey = config('services.roblox.open_cloud_api_key');

        if (! $apiKey) {
            throw new RuntimeException('Roblox Open Cloud API key is not configured. Add ROBLOX_OPEN_CLOUD_API_KEY in .env with Assets API permissions.');
        }

        return Http::withHeaders([
            'x-api-key' => $apiKey,
        ])->acceptJson();
    }

    private function sendUploadRequest(string $path, array $request, RobloxAccount $account, bool $retried = false): array
    {
        try {
            return $this->assetClient($account)
                ->send('POST', 'https://apis.roblox.com/assets/v1/assets', [
                    'multipart' => [
                        [
                            'name' => 'request',
                            'contents' => json_encode($request, JSON_THROW_ON_ERROR),
                        ],
                        [
                            'name' => 'fileContent',
                            'contents' => fopen($path, 'r'),
                            'filename' => basename($path),
                            'headers' => [
                                'Content-Type' => 'audio/ogg',
                            ],
                        ],
                    ],
                ])
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            if (! $retried && $this->shouldRefreshRobloxToken($exception)) {
                $this->oauth->refreshAccount($account);

                return $this->sendUploadRequest($path, $request, $account->refresh(), true);
            }

            throw $exception;
        }
    }

    private function getOperation(string $operationId, RobloxAccount $account, bool $retried = false): array
    {
        try {
            return $this->assetClient($account)
                ->get("https://apis.roblox.com/assets/v1/operations/{$operationId}")
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            if (! $retried && $this->shouldRefreshRobloxToken($exception)) {
                $this->oauth->refreshAccount($account);

                return $this->getOperation($operationId, $account->refresh(), true);
            }

            throw $exception;
        }
    }

    private function shouldRefreshRobloxToken(RequestException $exception): bool
    {
        if (config('services.roblox.upload_auth', 'oauth') !== 'oauth') {
            return false;
        }

        $status = $exception->response->status();
        $message = strtolower($exception->response->body());

        return in_array($status, [401, 403], true)
            && (str_contains($message, 'not authenticated') || str_contains($message, 'invalid token'));
    }

    private function creatorContext(RobloxAccount $account): array
    {
        if (config('services.roblox.upload_auth', 'oauth') === 'oauth') {
            return ['userId' => (int) $account->roblox_user_id];
        }

        $groupId = config('services.roblox.open_cloud_creator_group_id');
        if ($groupId) {
            return ['groupId' => (int) $groupId];
        }

        $userId = config('services.roblox.open_cloud_creator_user_id');
        if ($userId) {
            return ['userId' => (int) $userId];
        }

        return ['userId' => (int) $account->roblox_user_id];
    }
}
