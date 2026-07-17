<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Process;

class AudioMetadataService
{
    public function duration(string $path): ?float
    {
        $result = Process::run([
            config('converter.ffprobe_binary'),
            '-v',
            'error',
            '-show_entries',
            'format=duration',
            '-of',
            'default=noprint_wrappers=1:nokey=1',
            $path,
        ]);

        if (! $result->successful()) {
            return null;
        }

        $duration = trim($result->output());

        return is_numeric($duration) ? (float) $duration : null;
    }
}
