<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audio_files', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('conversion_preset_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('extension', 8);
            $table->string('original_path');
            $table->string('output_path')->nullable();
            $table->unsignedBigInteger('original_size');
            $table->unsignedBigInteger('output_size')->nullable();
            $table->decimal('duration', 10, 3)->nullable();
            $table->decimal('speed', 3, 1);
            $table->integer('amplify_db');
            $table->string('status')->index();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audio_files');
    }
};
