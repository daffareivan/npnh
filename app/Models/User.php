<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'avatar_path',
        'provider',
        'provider_id',
        'role',
        'last_login_at',
        'last_login_ip',
        'status',
        'community_banned_at',
        'credits_balance',
        'theme',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'community_banned_at' => 'datetime',
            'password' => 'hashed',
            'credits_balance' => 'integer',
            'upload_limit' => 'integer',
            'upload_unlimited' => 'boolean',
            'uploads_used' => 'integer',
        ];
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function hasReachedUploadLimit(): bool
    {
        return ! $this->upload_unlimited && $this->uploads_used >= $this->upload_limit;
    }

    public function audioFiles(): HasMany
    {
        return $this->hasMany(AudioFile::class);
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function creditHistories(): HasMany
    {
        return $this->hasMany(CreditHistory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reviewComments(): HasMany
    {
        return $this->hasMany(ReviewComment::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withPivot(['assigned_at', 'expires_at'])->withTimestamps()->orderByDesc('priority');
    }

    public function robloxAccount(): HasOne
    {
        return $this->hasOne(RobloxAccount::class);
    }
}
