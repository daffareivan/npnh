<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'credits',
        'badge',
        'description',
        'is_active',
        'is_custom',
        'features',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'credits' => 'integer',
            'is_active' => 'boolean',
            'is_custom' => 'boolean',
            'features' => 'array',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function formattedPrice(): string
    {
        if ($this->is_custom) {
            return 'Contact Admin';
        }

        return 'Rp '.number_format((int) $this->price, 0, ',', '.');
    }
}
