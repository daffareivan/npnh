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
        Schema::table('users', function (Blueprint $table): void {
            // Cumulative cap, meaningful only while upload_unlimited is false.
            // Grows every time a plan is activated; never reset.
            $table->unsignedInteger('upload_limit')->default(0)->after('credits_balance');
            $table->boolean('upload_unlimited')->default(false)->after('upload_limit');
            $table->unsignedInteger('uploads_used')->default(0)->after('upload_unlimited');
        });

        // Backfill lifetime usage from existing uploads so the new cap applies fairly going forward.
        DB::statement('UPDATE users SET uploads_used = (SELECT COUNT(*) FROM audio_files WHERE audio_files.user_id = users.id)');

        // Backfill cumulative allowance from every plan a user has ever been subscribed to
        // (mirrors the "adds per activation, never resets" rule going forward).
        $freePlanId = DB::table('plans')->where('slug', 'free')->value('id');

        DB::table('users')->select('id')->orderBy('id')->chunkById(200, function ($users) use ($freePlanId): void {
            foreach ($users as $user) {
                $planIds = DB::table('subscriptions')->where('user_id', $user->id)->pluck('plan_id');

                if ($planIds->isEmpty() && $freePlanId) {
                    $planIds = collect([$freePlanId]);
                }

                $maxUploadsValues = $planIds->isEmpty()
                    ? collect()
                    : DB::table('plans')->whereIn('id', $planIds)->pluck('max_uploads');

                $unlimited = $maxUploadsValues->contains(null);
                $limit = $unlimited ? 0 : $maxUploadsValues->sum();

                DB::table('users')->where('id', $user->id)->update([
                    'upload_limit' => $limit,
                    'upload_unlimited' => $unlimited,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['upload_limit', 'upload_unlimited', 'uploads_used']);
        });
    }
};
