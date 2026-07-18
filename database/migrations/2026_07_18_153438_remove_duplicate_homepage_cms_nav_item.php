<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('navigation_menus')->where('slug', 'nav-content-homepage')->delete();

        Cache::forget('navigation.admin.tree');
    }

    public function down(): void
    {
        $parent = DB::table('navigation_menus')->where('slug', 'nav-content')->first();

        if (! $parent) {
            return;
        }

        DB::table('navigation_menus')->updateOrInsert(
            ['slug' => 'nav-content-homepage'],
            [
                'parent_id' => $parent->id,
                'title' => 'Homepage CMS',
                'route_name' => 'admin.content.homepage-reviews',
                'icon' => 'layout-template',
                'permission' => 'admin.settings.manage',
                'sort_order' => 0,
                'type' => 'item',
                'is_active' => true,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Cache::forget('navigation.admin.tree');
    }
};
