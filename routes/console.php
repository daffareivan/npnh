<?php

use App\Models\AudioFile;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    $expiresAt = now()->subHours(config('converter.temporary_expiration_hours'));

    AudioFile::query()
        ->where('created_at', '<', $expiresAt)
        ->whereNotNull('output_path')
        ->each(function (AudioFile $audioFile): void {
            if (config('converter.auto_delete_files')) {
                Storage::delete(array_filter([$audioFile->original_path, $audioFile->output_path]));
            }
        });
})->hourly();
