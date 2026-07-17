<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewComment extends Model
{
    protected $fillable = ['review_id', 'parent_id', 'user_id', 'content', 'status', 'is_locked'];

    protected function casts(): array
    {
        return ['is_locked' => 'boolean'];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->where('status', 'approved')->with(['user.badges', 'replies.user.badges']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
