<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = ['user_id', 'rating', 'title', 'content', 'screenshot_path', 'status', 'is_pinned', 'is_featured', 'helpful_count'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_pinned' => 'boolean',
            'is_featured' => 'boolean',
            'helpful_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ReviewComment::class)->whereNull('parent_id')->with(['user.badges', 'replies.user.badges']);
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(ReviewComment::class);
    }

    public function helpfuls(): HasMany
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
}
