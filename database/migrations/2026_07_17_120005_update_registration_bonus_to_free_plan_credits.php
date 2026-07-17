<?php

use App\Services\CreditService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('credit_settings')->updateOrInsert(
            ['key' => CreditService::REGISTRATION_BONUS],
            ['value' => '8', 'updated_at' => now(), 'created_at' => now()]
        );
    }

    public function down(): void
    {
        DB::table('credit_settings')->where('key', CreditService::REGISTRATION_BONUS)->update(['value' => '100']);
    }
};
