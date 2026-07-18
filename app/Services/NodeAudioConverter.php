<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class NodeAudioConverter
{
    /**
     * Renders the input at the given speed (pitch shifts with it, matching the
     * reference audio-studio.html tool) with a 100Hz bass-shelf boost and peak
     * normalization, then encodes to Ogg Vorbis. No ffmpeg involved — the DSP
     * runs in headless Chromium via the Web Audio API.
     *
     * @return array{duration: float, size: int} duration/size of the produced
     *     (post speed-change) output — callers should persist this over the
     *     original file's duration, since the two differ whenever speed != 1.
     */
    public function convert(string $inputPath, string $outputPath, float $speed, int $amplifyDb): array
    {
        // Stored amplify_db has historically been negative (a volume cut); the
        // reference tool always boosts the bass shelf, so the sign is irrelevant here.
        $bassGainDb = abs($amplifyDb);

        $result = Process::timeout((int) config('converter.audio_engine_timeout'))->run([
            config('converter.node_binary'),
            config('converter.audio_engine_path'),
            'convert',
            $inputPath,
            $outputPath,
            (string) $speed,
            (string) $bassGainDb,
            (string) config('converter.ogg_vbr_quality'),
        ]);

        if (! $result->successful()) {
            throw new RuntimeException($result->errorOutput() ?: $result->output() ?: 'Audio conversion failed.');
        }

        $data = json_decode($result->output(), true);

        return [
            'duration' => (float) ($data['duration'] ?? 0),
            'size' => (int) ($data['size'] ?? 0),
        ];
    }
}
