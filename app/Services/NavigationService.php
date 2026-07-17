<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NavigationMenu;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class NavigationService
{
    public function adminTree(?User $user): Collection
    {
        return Cache::remember('navigation.admin.tree', now()->addMinutes(10), function (): Collection {
            return NavigationMenu::query()
                ->enabled()
                ->whereNull('parent_id')
                ->with(['children' => fn ($query) => $query->enabled()->orderBy('sort_order')->orderBy('title')])
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get();
        })->map(fn (NavigationMenu $menu) => $this->filterNode($menu, $user))
            ->filter()
            ->values();
    }

    public function isActive(NavigationMenu $menu): bool
    {
        if ($menu->route_name && request()->routeIs($menu->route_name)) {
            return true;
        }

        return $menu->children->contains(fn (NavigationMenu $child) => $this->isActive($child));
    }

    public function href(NavigationMenu $menu): string
    {
        if ($menu->route_name && Route::has($menu->route_name)) {
            return route($menu->route_name);
        }

        return $menu->url ?: '#';
    }

    public function title(NavigationMenu $menu): string
    {
        $key = Str::of($menu->slug)
            ->replaceStart('nav-', '')
            ->replace('-', '_')
            ->toString();

        $translation = __("navigation.$key");

        if ($translation !== "navigation.$key") {
            return $translation;
        }

        $fallbackKey = Str::of($menu->title)->lower()->replace([' & ', ' ', '-'], '_')->toString();
        $fallback = __("navigation.$fallbackKey");

        return $fallback !== "navigation.$fallbackKey" ? $fallback : $menu->title;
    }

    private function filterNode(NavigationMenu $menu, ?User $user): ?NavigationMenu
    {
        if (! $this->canSee($menu, $user)) {
            return null;
        }

        $menu->setRelation(
            'children',
            $menu->children
                ->map(fn (NavigationMenu $child) => $this->filterNode($child, $user))
                ->filter()
                ->values()
        );

        return $menu;
    }

    private function canSee(NavigationMenu $menu, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($menu->role && ! $user->hasRole($menu->role)) {
            return false;
        }

        if ($menu->permission && ! $user->can($menu->permission)) {
            return false;
        }

        if ($menu->module === 'roblox' && ! config('services.roblox.client_id')) {
            return false;
        }

        return true;
    }
}
