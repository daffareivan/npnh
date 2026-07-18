<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ConversionFile;
use App\Models\User;

class ConversionFilePolicy
{
    public function view(?User $user, ConversionFile $conversionFile): bool
    {
        return $user?->can('admin.conversions.manage') || $user?->id === $conversionFile->audioFile->user_id;
    }

    public function download(?User $user, ConversionFile $conversionFile): bool
    {
        return $user?->can('admin.conversions.manage') || ($user?->can('converter.download.own') && $user->id === $conversionFile->audioFile->user_id);
    }
}
