<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'invoice_number',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'status',
        'amount',
        'started_at',
        'expired_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expired_at' => 'datetime',
            'paid_at' => 'datetime',
            'amount' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
