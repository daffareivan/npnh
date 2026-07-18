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

        return $this->segment($audioFile, $fullPath, $outputPath, $maxSeconds);
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
    private function segment(AudioFile $audioFile, string $fullPath, string $outputPath, int $maxSeconds): Collection
    {
        $segmentDir = config('converter.storage_path').'/segments/'.$audioFile->id;
        Storage::deleteDirectory($segmentDir);
        Storage::makeDirectory($segmentDir);
        $pattern = Storage::path($segmentDir).'/part_%03d.ogg';

        // -c copy: stream-copies the already-encoded OGG/Vorbis packets into new
        // segment containers instead of re-encoding, so this is fast and lossless.
        $result = Process::timeout(600)->run([
            config('converter.ffmpeg_binary'),
            '-y',
            '-i',
            $fullPath,
            '-f',
            'segment',
            '-segment_time',
            (string) $maxSeconds,
            '-reset_timestamps',
            '1',
            '-c',
            'copy',
            $pattern,
        ]);

        if (! $result->successful()) {
            throw new RuntimeException($result->errorOutput() ?: 'Audio split failed.');
        }

        $baseName = pathinfo((string) $audioFile->original_name, PATHINFO_FILENAME);

        $partPaths = collect(Storage::files($segmentDir))
            ->filter(fn (string $path): bool => str_ends_with($path, '.ogg'))
            ->sort()
            ->values();

        if ($partPaths->isEmpty()) {
            throw new RuntimeException('Audio split produced no output files.');
        }

        // The segment muxer can emit a near-empty trailing artifact when the
        // source duration lands exactly on a segment boundary; drop those.
        $parts = $partPaths
            ->map(fn (string $path): array => ['path' => $path, 'duration' => (float) ($this->metadata->duration(Storage::path($path)) ?? 0)])
            ->filter(fn (array $part): bool => $part['duration'] >= 1.0)
            ->values();

        if ($parts->isEmpty()) {
            throw new RuntimeException('Audio split produced no usable output files.');
        }

        foreach ($partPaths as $path) {
            if (! $parts->contains('path', $path)) {
                Storage::delete($path);
            }
        }

        ConversionFile::query()->where('audio_file_id', $audioFile->id)
            ->whereNotIn('sequence', range(1, $parts->count()))
            ->delete();

        return $parts->map(function (array $part, int $index) use ($audioFile, $baseName): ConversionFile {
            $sequence = $index + 1;

            return ConversionFile::query()->updateOrCreate(
                ['audio_file_id' => $audioFile->id, 'sequence' => $sequence],
                [
                    'file_name' => "{$baseName} - Part {$sequence}.ogg",
                    'file_path' => $part['path'],
                    'duration' => $part['duration'],
                    'size' => Storage::size($part['path']),
                    'status' => 'ready',
                ]
            );
        })->values();
    }

    /**
     * Generates lightweight waveform peak data (0..1 amplitude per bucket) from a
     * heavily downsampled mono 8kHz PCM proxy — not the original high-quality
     * audio — so this never loads the source audio into memory.
     *
     * @return array<int, float>
     */
    public function generatePeaks(string $absolutePath, int $samples = 100): array
    {
        $result = Process::timeout(120)->run([
            config('converter.ffmpeg_binary'),
            '-i',
            $absolutePath,
            '-ac',
            '1',
            '-ar',
            '8000',
            '-f',
            'u8',
            '-acodec',
            'pcm_u8',
            'pipe:1',
        ]);

        if (! $result->successful()) {
            return [];
        }

        $bytes = $result->output();
        $total = strlen($bytes);

        if ($total === 0) {
            return [];
        }

        $bucketSize = max(1, (int) floor($total / $samples));
        $peaks = [];

        for ($i = 0; $i < $samples && ($i * $bucketSize) < $total; $i++) {
            $chunk = substr($bytes, $i * $bucketSize, $bucketSize);
            $peak = 0;

            for ($j = 0, $len = strlen($chunk); $j < $len; $j++) {
                $amplitude = abs(ord($chunk[$j]) - 128);
                $peak = max($peak, $amplitude);
            }

            $peaks[] = round($peak / 128, 3);
        }

        return $peaks;
    }
}
