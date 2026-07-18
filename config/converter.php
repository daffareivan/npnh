<?php

declare(strict_types=1);

return [
    'storage_path' => env('CONVERTER_STORAGE_PATH', 'converter'),
    'temporary_expiration_hours' => (int) env('CONVERTER_TEMP_EXPIRATION_HOURS', 24),
    'max_upload_size_mb' => (int) env('CONVERTER_MAX_UPLOAD_SIZE_MB', 100),
    'queue' => env('CONVERTER_QUEUE', 'audio-conversion'),
    'max_segment_seconds' => (int) env('CONVERTER_MAX_SEGMENT_SECONDS', 360),
    'auto_delete_files' => (bool) env('CONVERTER_AUTO_DELETE_FILES', false),
    'default_output_format' => env('CONVERTER_DEFAULT_OUTPUT_FORMAT', 'ogg'),
    'ffmpeg_binary' => env('FFMPEG_BINARY', 'ffmpeg'),
    'ffprobe_binary' => env('FFPROBE_BINARY', 'ffprobe'),
];
