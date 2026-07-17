<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\AudioFile;
use Illuminate\Foundation\Events\Dispatchable;

class RobloxUploadCompleted
{
    use Dispatchable;

    public function __construct(public readonly AudioFile $audioFile) {}
}
