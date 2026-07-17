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
        Schema::create('conversion_presets', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->decimal('speed', 3, 1);
            $table->integer('amplify_db');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        DB::table('conversion_presets')->insert([
            ['name' => '2.3x', 'speed' => 2.3, 'amplify_db' => -4, 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '2.5x', 'speed' => 2.5, 'amplify_db' => -6, 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '2.7x', 'speed' => 2.7, 'amplify_db' => -8, 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('conversion_presets');
    }
};
