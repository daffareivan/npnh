<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class FfmpegAudioConverter
{
    public function convert(string $inputPath, string $outputPath, float $speed, int $amplifyDb): void
    {
        $atempoChain = $this->buildAtempoChain($speed);

        $result = Process::timeout(600)->run([
            config('converter.ffmpeg_binary'),
            '-y',
            '-i',
            $inputPath,
            '-filter:a',
            "{$atempoChain},volume={$amplifyDb}dB",
            '-vn',
            '-c:a',
            'libvorbis',
            $outputPath,
        ]);

        if (! $result->successful()) {
            throw new RuntimeException($result->errorOutput() ?: 'FFmpeg conversion failed.');
        }
    }

    private function buildAtempoChain(float $speed): string
    {
        $filters = [];

        while ($speed > 2.0) {
            $filters[] = 'atempo=2.0';
            $speed /= 2.0;
        }

        $filters[] = 'atempo='.round($speed, 4);

        return implode(',', $filters);
    }
}
