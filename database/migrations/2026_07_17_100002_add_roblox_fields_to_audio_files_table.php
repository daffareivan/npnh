<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audio_files', function (Blueprint $table): void {
            $table->string('roblox_status')->default('pending')->after('status');
            $table->string('roblox_asset_id')->nullable()->after('roblox_status');
            $table->string('roblox_creator_url')->nullable()->after('roblox_asset_id');
            $table->text('roblox_error_message')->nullable()->after('roblox_creator_url');
        });
    }

    public function down(): void
    {
        Schema::table('audio_files', function (Blueprint $table): void {
            $table->dropColumn([
                'roblox_status',
                'roblox_asset_id',
                'roblox_creator_url',
                'roblox_error_message',
            ]);
        });
    }
};
