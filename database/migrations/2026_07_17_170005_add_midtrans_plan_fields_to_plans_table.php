<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table): void {
            $table->string('badge')->nullable()->after('credits');
            $table->text('description')->nullable()->after('badge');
            $table->boolean('is_active')->default(true)->after('description');
        });

        DB::table('plans')->update(['is_active' => true]);
        DB::table('plans')->where('slug', 'standard')->update(['badge' => 'Most Popular']);
        DB::table('plans')->where('slug', 'premium')->update(['badge' => 'Best Value']);
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table): void {
            $table->dropColumn(['badge', 'description', 'is_active']);
        });
    }
};
