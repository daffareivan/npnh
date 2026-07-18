<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentTransaction extends Model
{
    public const PROVIDER_MIDTRANS = 'midtrans';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'provider',
        'order_id',
        'provider_transaction_id',
        'payment_type',
        'gross_amount',
        'transaction_status',
        'fraud_status',
        'signature_key',
        'raw_response',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'raw_response' => 'array',
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

    public function creditHistories(): HasMany
    {
        return $this->hasMany(CreditHistory::class);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->whereIn('transaction_status', ['settlement', 'capture']);
    }

    public function isPaid(): bool
    {
        return in_array($this->transaction_status, ['settlement', 'capture'], true);
    }

    public function formattedAmount(): string
    {
        return 'Rp '.number_format($this->gross_amount, 0, ',', '.');
    }
}
