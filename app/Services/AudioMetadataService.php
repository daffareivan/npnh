<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Process;

class AudioMetadataService
{
    public function duration(string $path): ?float
    {
        $result = Process::timeout((int) config('converter.audio_engine_timeout'))->run([
            config('converter.node_binary'),
            config('converter.audio_engine_path'),
            'probe',
            $path,
        ]);

        if (! $result->successful()) {
            return null;
        }

        $data = json_decode($result->output(), true);

        return isset($data['duration']) ? (float) $data['duration'] : null;
    }
}
