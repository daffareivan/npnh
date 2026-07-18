<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversion_files', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('audio_file_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sequence')->default(1);
            $table->string('file_name');
            $table->string('file_path');
            $table->decimal('duration', 10, 3)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('status')->default('ready')->index();
            $table->json('waveform_peaks')->nullable();
            $table->string('roblox_status')->nullable();
            $table->string('roblox_asset_id')->nullable();
            $table->string('roblox_creator_url')->nullable();
            $table->text('roblox_error_message')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->unique(['audio_file_id', 'sequence']);
        });

        Schema::table('download_logs', function (Blueprint $table): void {
            $table->foreignId('conversion_file_id')->nullable()->after('audio_file_id')->constrained()->nullOnDelete();
        });

        // Backfill: every existing finished conversion gets exactly one ConversionFile
        // wrapping its current single output, so the new per-file system covers all
        // historical data uniformly (no old/new special-casing needed elsewhere).
        $now = now();
        DB::table('audio_files')
            ->where('status', 'finished')
            ->whereNotNull('output_path')
            ->orderBy('id')
            ->chunkById(200, function ($audioFiles) use ($now): void {
                foreach ($audioFiles as $audioFile) {
                    DB::table('conversion_files')->insert([
                        'audio_file_id' => $audioFile->id,
                        'sequence' => 1,
                        'file_name' => pathinfo((string) $audioFile->original_name, PATHINFO_FILENAME).'.ogg',
                        'file_path' => $audioFile->output_path,
                        'duration' => $audioFile->duration,
                        'size' => $audioFile->output_size,
                        'status' => 'ready',
                        'roblox_status' => $audioFile->roblox_status,
                        'roblox_asset_id' => $audioFile->roblox_asset_id,
                        'roblox_creator_url' => $audioFile->roblox_creator_url,
                        'roblox_error_message' => $audioFile->roblox_error_message,
                        'created_at' => $audioFile->created_at ?? $now,
                        'updated_at' => $now,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('download_logs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('conversion_file_id');
        });

        Schema::dropIfExists('conversion_files');
    }
};
