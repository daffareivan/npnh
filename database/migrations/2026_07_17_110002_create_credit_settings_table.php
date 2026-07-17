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
        Schema::create('credit_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });

        DB::table('credit_settings')->insert([
            ['key' => 'registration_bonus', 'value' => '100', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'download_cost', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'roblox_upload_cost', 'value' => '2', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'allow_negative_balance', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'refund_failed_upload', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_settings');
    }
};
