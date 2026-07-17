<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('contact_settings')->insert([
            ['key' => 'whatsapp', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'telegram', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'discord', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'email', 'value' => config('mail.from.address'), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'preferred_channel', 'value' => 'whatsapp', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
