<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * These nav items pointed to the exact same route as a sibling item
     * (admin.history and admin.subscription.orders each had multiple menu
     * entries), which made several sidebar items highlight as "active"
     * simultaneously for a single page. Keeping just one item per route.
     */
    private const REMOVED_SLUGS = [
        'nav-converter-downloads',
        'nav-converter-roblox-upload',
        'nav-business-payments',
    ];

    public function up(): void
    {
        DB::table('navigation_menus')->whereIn('slug', self::REMOVED_SLUGS)->delete();

        Cache::forget('navigation.admin.tree');
    }

    public function down(): void
    {
        $converterParent = DB::table('navigation_menus')->where('slug', 'nav-converter')->value('id');
        $businessParent = DB::table('navigation_menus')->where('slug', 'nav-business')->value('id');
        $now = now();

        if ($converterParent) {
            DB::table('navigation_menus')->updateOrInsert(
                ['slug' => 'nav-converter-downloads'],
                ['parent_id' => $converterParent, 'title' => 'Downloads', 'route_name' => 'admin.history', 'icon' => 'download', 'permission' => 'admin.conversions.manage', 'sort_order' => 30, 'type' => 'item', 'role' => 'admin', 'is_active' => true, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now]
            );
            DB::table('navigation_menus')->updateOrInsert(
                ['slug' => 'nav-converter-roblox-upload'],
                ['parent_id' => $converterParent, 'title' => 'Roblox Upload', 'route_name' => 'admin.history', 'icon' => 'cloud', 'permission' => 'admin.conversions.manage', 'module' => 'roblox', 'sort_order' => 40, 'type' => 'item', 'role' => 'admin', 'is_active' => true, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        if ($businessParent) {
            DB::table('navigation_menus')->updateOrInsert(
                ['slug' => 'nav-business-payments'],
                ['parent_id' => $businessParent, 'title' => 'Payments', 'route_name' => 'admin.subscription.orders', 'icon' => 'receipt', 'permission' => 'admin.credits.manage', 'sort_order' => 40, 'type' => 'item', 'role' => 'admin', 'is_active' => true, 'is_visible' => true, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        Cache::forget('navigation.admin.tree');
    }
};
