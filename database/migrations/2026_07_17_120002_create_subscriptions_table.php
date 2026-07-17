<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });

        $freePlanId = DB::table('plans')->where('slug', 'free')->value('id');
        if ($freePlanId) {
            $now = now();
            DB::table('users')->orderBy('id')->select('id')->chunk(100, function ($users) use ($freePlanId, $now): void {
                $rows = $users->map(fn ($user) => [
                    'user_id' => $user->id,
                    'plan_id' => $freePlanId,
                    'status' => 'active',
                    'started_at' => $now,
                    'expired_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                DB::table('subscriptions')->insert($rows);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
