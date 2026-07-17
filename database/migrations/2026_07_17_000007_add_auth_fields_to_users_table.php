<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('avatar')->nullable()->after('password');
            $table->string('provider')->default('local')->index()->after('avatar');
            $table->string('provider_id')->nullable()->index()->after('provider');
            $table->string('role')->default('user')->index()->after('provider_id');
            $table->timestamp('last_login_at')->nullable()->after('role');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['avatar', 'provider', 'provider_id', 'role', 'last_login_at', 'last_login_ip']);
        });
    }
};
