<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NavigationMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class NavigationMenuSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->menus() as $section) {
            $parent = NavigationMenu::query()->updateOrCreate(
                ['slug' => $section['slug']],
                [
                    'title' => $section['title'],
                    'icon' => $section['icon'] ?? null,
                    'sort_order' => $section['sort_order'],
                    'type' => 'section',
                    'role' => 'admin',
                    'is_active' => true,
                    'is_visible' => true,
                ]
            );

            foreach ($section['items'] as $index => $item) {
                NavigationMenu::query()->updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'parent_id' => $parent->id,
                        'title' => $item['title'],
                        'route_name' => $item['route_name'] ?? null,
                        'url' => $item['url'] ?? null,
                        'icon' => $item['icon'] ?? null,
                        'badge' => $item['badge'] ?? null,
                        'badge_color' => $item['badge_color'] ?? null,
                        'permission' => $item['permission'] ?? null,
                        'role' => $item['role'] ?? 'admin',
                        'sort_order' => $item['sort_order'] ?? (($index + 1) * 10),
                        'type' => 'item',
                        'is_active' => $item['is_active'] ?? true,
                        'is_visible' => $item['is_visible'] ?? true,
                        'open_in_new_tab' => $item['open_in_new_tab'] ?? false,
                        'module' => $item['module'] ?? null,
                    ]
                );
            }
        }

        Cache::forget('navigation.admin.tree');
    }

    private function menus(): array
    {
        return [
            [
                'title' => 'Dashboard',
                'slug' => 'nav-dashboard',
                'icon' => 'layout-dashboard',
                'sort_order' => 10,
                'items' => [
                    ['title' => 'Overview', 'slug' => 'nav-dashboard-overview', 'route_name' => 'admin.dashboard.show', 'icon' => 'layout-dashboard', 'permission' => 'admin.access'],
                    ['title' => 'Analytics', 'slug' => 'nav-dashboard-analytics', 'route_name' => 'admin.analytics', 'icon' => 'activity', 'permission' => 'admin.analytics.view'],
                ],
            ],
            [
                'title' => 'Converter',
                'slug' => 'nav-converter',
                'icon' => 'music',
                'sort_order' => 20,
                'items' => [
                    ['title' => 'Open Converter', 'slug' => 'nav-converter-open', 'route_name' => 'app.converter', 'icon' => 'music', 'permission' => 'converter.convert'],
                    ['title' => 'Conversions', 'slug' => 'nav-converter-conversions', 'route_name' => 'admin.history', 'icon' => 'database', 'permission' => 'admin.conversions.manage'],
                ],
            ],
            [
                'title' => 'Business',
                'slug' => 'nav-business',
                'icon' => 'credit-card',
                'sort_order' => 30,
                'items' => [
                    ['title' => 'Subscriptions', 'slug' => 'nav-business-subscriptions', 'route_name' => 'admin.subscription.orders', 'icon' => 'package', 'permission' => 'admin.credits.manage'],
                    ['title' => 'Plans', 'slug' => 'nav-business-plans', 'route_name' => 'admin.subscription.plans', 'icon' => 'package', 'permission' => 'admin.credits.manage'],
                    ['title' => 'Credits', 'slug' => 'nav-business-credits', 'route_name' => 'admin.credit-settings.edit', 'icon' => 'credit-card', 'permission' => 'admin.credits.manage'],
                    ['title' => 'Transactions', 'slug' => 'nav-business-transactions', 'route_name' => 'admin.subscription.transactions', 'icon' => 'activity', 'permission' => 'admin.credits.manage'],
                ],
            ],
            [
                'title' => 'Content',
                'slug' => 'nav-content',
                'icon' => 'message-circle',
                'sort_order' => 40,
                'items' => [
                    ['title' => 'Reviews', 'slug' => 'nav-content-reviews', 'route_name' => 'admin.content.homepage-reviews', 'icon' => 'star', 'permission' => 'admin.settings.manage'],
                    ['title' => 'Comments', 'slug' => 'nav-content-comments', 'route_name' => 'admin.community.comments', 'icon' => 'message-circle', 'permission' => 'admin.settings.manage'],
                    ['title' => 'Reports', 'slug' => 'nav-content-reports', 'route_name' => 'admin.community.reports', 'icon' => 'shield-alert', 'permission' => 'admin.settings.manage'],
                    ['title' => 'Badges', 'slug' => 'nav-content-badges', 'route_name' => 'admin.community.badges', 'icon' => 'badge-check', 'permission' => 'admin.settings.manage'],
                ],
            ],
            [
                'title' => 'Users & Access',
                'slug' => 'nav-users-access',
                'icon' => 'users',
                'sort_order' => 50,
                'items' => [
                    ['title' => 'Users', 'slug' => 'nav-users', 'route_name' => 'admin.users.index', 'icon' => 'users', 'permission' => 'admin.users.manage'],
                    ['title' => 'Activity Log', 'slug' => 'nav-activity-log', 'route_name' => 'admin.activity', 'icon' => 'activity', 'permission' => 'admin.activity.view'],
                ],
            ],
            [
                'title' => 'System',
                'slug' => 'nav-system',
                'icon' => 'settings',
                'sort_order' => 60,
                'items' => [
                    ['title' => 'Integrations', 'slug' => 'nav-system-integrations', 'route_name' => 'app.integrations', 'icon' => 'plug', 'permission' => 'admin.settings.manage'],
                    ['title' => 'Queue Monitor', 'slug' => 'nav-system-queue', 'route_name' => 'admin.queue', 'icon' => 'server', 'permission' => 'admin.queue.manage'],
                    ['title' => 'Converter Settings', 'slug' => 'nav-system-converter-settings', 'route_name' => 'admin.settings', 'icon' => 'sliders-horizontal', 'permission' => 'admin.settings.manage'],
                    ['title' => 'App Settings', 'slug' => 'nav-system-app-settings', 'route_name' => 'admin.app-settings.edit', 'icon' => 'settings', 'permission' => 'admin.settings.manage'],
                    ['title' => 'Contact Settings', 'slug' => 'nav-system-contact-settings', 'route_name' => 'admin.subscription.contact-settings', 'icon' => 'mail', 'permission' => 'admin.settings.manage'],
                ],
            ],
        ];
    }
}
