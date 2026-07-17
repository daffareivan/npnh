<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_menus', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_menus')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->string('badge_color')->nullable();
            $table->string('permission')->nullable();
            $table->string('role')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('type')->default('item')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->boolean('open_in_new_tab')->default(false);
            $table->string('module')->nullable()->index();
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
            $table->index(['route_name', 'url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_menus');
    }
};
