<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('badge');
            $table->string('color')->default('gray');
            $table->unsignedInteger('priority')->default(0);
            $table->string('visibility')->default('public');
            $table->string('auto_assign_rule')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('user_badges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'badge_id']);
        });

        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('title');
            $table->text('content');
            $table->string('screenshot_path')->nullable();
            $table->string('status')->default('approved');
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();
            $table->unique('user_id');
        });

        Schema::create('review_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('review_comments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->string('status')->default('approved');
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        Schema::create('review_helpful', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['review_id', 'user_id']);
        });

        $now = now();
        DB::table('badges')->insert([
            ['name' => 'Free', 'slug' => 'free', 'icon' => 'badge', 'color' => 'gray', 'priority' => 10, 'visibility' => 'public', 'auto_assign_rule' => 'plan:free', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Standard', 'slug' => 'standard', 'icon' => 'shield-check', 'color' => 'blue', 'priority' => 20, 'visibility' => 'public', 'auto_assign_rule' => 'plan:standard', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Premium', 'slug' => 'premium', 'icon' => 'gem', 'color' => 'purple', 'priority' => 30, 'visibility' => 'public', 'auto_assign_rule' => 'plan:premium', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Enterprise', 'slug' => 'enterprise', 'icon' => 'crown', 'color' => 'gold', 'priority' => 40, 'visibility' => 'public', 'auto_assign_rule' => 'plan:custom', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Verified', 'slug' => 'verified', 'icon' => 'badge-check', 'color' => 'green', 'priority' => 50, 'visibility' => 'public', 'auto_assign_rule' => null, 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Founder', 'slug' => 'founder', 'icon' => 'crown', 'color' => 'founder', 'priority' => 60, 'visibility' => 'public', 'auto_assign_rule' => null, 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Developer', 'slug' => 'developer', 'icon' => 'code-2', 'color' => 'cyan', 'priority' => 45, 'visibility' => 'public', 'auto_assign_rule' => null, 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('review_helpful');
        Schema::dropIfExists('review_comments');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
    }
};
