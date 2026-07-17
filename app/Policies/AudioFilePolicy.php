<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AudioFile;
use App\Models\User;

class AudioFilePolicy
{
    public function create(?User $user): bool
    {
        return (bool) $user?->can('converter.upload');
    }

    public function view(?User $user, AudioFile $audioFile): bool
    {
        return $user?->can('admin.conversions.manage') || $user?->id === $audioFile->user_id;
    }

    public function delete(?User $user, AudioFile $audioFile): bool
    {
        return $this->view($user, $audioFile);
    }

    public function download(?User $user, AudioFile $audioFile): bool
    {
        return $user?->can('admin.conversions.manage') || ($user?->can('converter.download.own') && $user->id === $audioFile->user_id);
    }
}
