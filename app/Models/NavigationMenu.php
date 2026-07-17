<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenu extends Model
{
    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'route_name',
        'url',
        'icon',
        'badge',
        'badge_color',
        'permission',
        'role',
        'sort_order',
        'type',
        'is_active',
        'is_visible',
        'open_in_new_tab',
        'module',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_visible' => 'boolean',
            'open_in_new_tab' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('title');
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('is_visible', true);
    }
}
