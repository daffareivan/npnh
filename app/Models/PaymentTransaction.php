<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentTransaction extends Model
{
    public const PROVIDER_MIDTRANS = 'midtrans';
    public const PROVIDER_MUSTIKA = 'mustika';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'invoice_number',
        'payment_gateway',
        'payment_reference',
        'gateway_transaction_id',
        'user_id',
        'plan_id',
        'subscription_id',
        'provider',
        'order_id',
        'provider_transaction_id',
        'payment_type',
        'gross_amount',
        'amount',
        'fee',
        'status',
        'payment_method',
        'expired_at',
        'transaction_status',
        'fraud_status',
        'signature_key',
        'callback_payload',
        'raw_response',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'amount' => 'integer',
            'fee' => 'integer',
            'expired_at' => 'datetime',
            'callback_payload' => 'array',
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
        return in_array($this->transaction_status, ['settlement', 'capture', self::STATUS_PAID], true);
    }

    public function formattedAmount(): string
    {
        return 'Rp '.number_format($this->amount ?: $this->gross_amount, 0, ',', '.');
    }
}
