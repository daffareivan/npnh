<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'color', 'priority', 'visibility', 'auto_assign_rule', 'is_system'];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'is_system' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')->withPivot(['assigned_at', 'expires_at'])->withTimestamps();
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visibility', 'public');
    }
}
