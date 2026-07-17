<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('app_settings')) {
            return;
        }

        foreach ([
            'theme_default' => 'system',
            'locale_default' => 'en',
            'allow_theme_switch' => '1',
            'allow_language_switch' => '1',
        ] as $key => $value) {
            DB::table('app_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('app_settings')) {
            return;
        }

        DB::table('app_settings')->whereIn('key', [
            'theme_default',
            'locale_default',
            'allow_theme_switch',
            'allow_language_switch',
        ])->delete();
    }
};
