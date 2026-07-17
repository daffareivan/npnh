<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ConversionStatus;
use App\Events\AudioConversionStatusChanged;
use App\Models\AudioFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AudioFileRepository
{
    public function create(array $attributes): AudioFile
    {
        return AudioFile::query()->create($attributes);
    }

    public function paginate(?int $userId = null, int $perPage = 10): LengthAwarePaginator
    {
        return AudioFile::query()
            ->with('preset')
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->latest()
            ->paginate($perPage);
    }

    public function updateStatus(AudioFile $audioFile, ConversionStatus $status, ?string $error = null): AudioFile
    {
        $audioFile->forceFill([
            'status' => $status,
            'progress' => $status->progress(),
            'error_message' => $error,
        ])->save();

        AudioConversionStatusChanged::dispatch($audioFile);

        return $audioFile->refresh();
    }
}
