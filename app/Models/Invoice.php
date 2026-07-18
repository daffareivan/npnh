<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'invoice_number',
        'status',
        'subtotal',
        'tax',
        'total',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'tax' => 'integer',
            'total' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function formattedTotal(): string
    {
        return 'Rp '.number_format($this->total, 0, ',', '.');
    }
}
