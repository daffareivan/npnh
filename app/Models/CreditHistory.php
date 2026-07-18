<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditHistory extends Model
{
    protected $fillable = [
        'user_id',
        'payment_transaction_id',
        'type',
        'credits',
        'balance_before',
        'balance_after',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'balance_before' => 'integer',
            'balance_after' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class);
    }
}
