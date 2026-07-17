<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\AudioFile;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AudioConversionStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly AudioFile $audioFile) {}
}
