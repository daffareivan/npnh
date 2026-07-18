<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AudioFile;
use App\Models\ConversionFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AudioSplitService
{
    public function __construct(private readonly AudioMetadataService $metadata) {}

    /**
     * Splits the audio file's output into one or more ConversionFile records.
     * Files at or under the configured max segment length produce a single
     * ConversionFile wrapping the existing output (no re-encode, no copy).
     *
     * @return Collection<int, ConversionFile>
     */
    public function split(AudioFile $audioFile): Collection
    {
        $maxSeconds = max(1, (int) config('converter.max_segment_seconds', 360));
        $outputPath = $audioFile->output_path;
        $fullPath = Storage::path($outputPath);
        $duration = (float) ($audioFile->duration ?: ($this->metadata->duration($fullPath) ?? 0));

        if ($duration <= $maxSeconds) {
            return collect([$this->storeSingleFile($audioFile, $outputPath, $duration)]);
        }

        return $this->segment($audioFile, $fullPath, $maxSeconds);
    }

    private function storeSingleFile(AudioFile $audioFile, string $outputPath, float $duration): ConversionFile
    {
        return ConversionFile::query()->updateOrCreate(
            ['audio_file_id' => $audioFile->id, 'sequence' => 1],
            [
                'file_name' => pathinfo((string) $audioFile->original_name, PATHINFO_FILENAME).'.ogg',
                'file_path' => $outputPath,
                'duration' => $duration,
                'size' => Storage::exists($outputPath) ? Storage::size($outputPath) : null,
                'status' => 'ready',
            ]
        );
    }

    /**
     * @return Collection<int, ConversionFile>
     */
    private function segment(AudioFile $audioFile, string $fullPath, int $maxSeconds): Collection
    {
        $segmentDir = config('converter.storage_path').'/segments/'.$audioFile->id;
        Storage::deleteDirectory($segmentDir);
        Storage::makeDirectory($segmentDir);

        // Splits the already-converted output evenly (CEILING(duration/max) parts,
        // not fixed-length+remainder) and re-encodes each part to Ogg Vorbis inside
        // headless Chromium — no ffmpeg involved. See audio-engine/engine.mjs.
        $result = Process::timeout((int) config('converter.audio_engine_timeout'))->run([
            config('converter.node_binary'),
            config('converter.audio_engine_path'),
            'split',
            $fullPath,
            Storage::path($segmentDir),
            (string) $maxSeconds,
            pathinfo((string) $audioFile->original_name, PATHINFO_FILENAME),
            (string) config('converter.ogg_vbr_quality'),
        ]);

        if (! $result->successful()) {
            throw new RuntimeException($result->errorOutput() ?: 'Audio split failed.');
        }

        $data = json_decode($result->output(), true);
        $parts = collect($data['parts'] ?? [])
            ->filter(fn (array $part): bool => ($part['duration'] ?? 0) >= 1.0)
            ->values();

        if ($parts->isEmpty()) {
            throw new RuntimeException('Audio split produced no usable output files.');
        }

        $baseName = pathinfo((string) $audioFile->original_name, PATHINFO_FILENAME);

        ConversionFile::query()->where('audio_file_id', $audioFile->id)
            ->whereNotIn('sequence', range(1, $parts->count()))
            ->delete();

        return $parts->map(function (array $part, int $index) use ($audioFile, $baseName): ConversionFile {
            $sequence = $index + 1;
            $relativePath = $this->relativePath($part['path']);

            return ConversionFile::query()->updateOrCreate(
                ['audio_file_id' => $audioFile->id, 'sequence' => $sequence],
                [
                    'file_name' => "{$baseName} - Part {$sequence}.ogg",
                    'file_path' => $relativePath,
                    'duration' => $part['duration'],
                    'size' => $part['size'] ?? (Storage::exists($relativePath) ? Storage::size($relativePath) : null),
                    'status' => 'ready',
                ]
            );
        })->values();
    }

    private function relativePath(string $absolutePath): string
    {
        $root = rtrim(Storage::path(''), '/\\');
        $normalized = str_replace('\\', '/', $absolutePath);
        $normalizedRoot = str_replace('\\', '/', $root);

        return ltrim(str_replace($normalizedRoot, '', $normalized), '/');
    }

    /**
     * Generates lightweight waveform peak data (0..1 amplitude per bucket) by
     * decoding the file in headless Chromium and taking the max abs sample per
     * bucket directly off the decoded Float32 PCM — no ffmpeg involved.
     *
     * @return array<int, float>
     */
    public function generatePeaks(string $absolutePath, int $samples = 100): array
    {
        $result = Process::timeout((int) config('converter.audio_engine_timeout'))->run([
            config('converter.node_binary'),
            config('converter.audio_engine_path'),
            'peaks',
            $absolutePath,
            (string) $samples,
        ]);

        if (! $result->successful()) {
            return [];
        }

        $data = json_decode($result->output(), true);

        return $data['peaks'] ?? [];
    }
}
