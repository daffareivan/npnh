<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CreditSetting;
use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreditService
{
    public const DOWNLOAD_COST = 'download_cost';

    public const ROBLOX_UPLOAD_COST = 'roblox_upload_cost';

    public const REGISTRATION_BONUS = 'registration_bonus';

    public const ALLOW_NEGATIVE_BALANCE = 'allow_negative_balance';

    public const REFUND_FAILED_UPLOAD = 'refund_failed_upload';

    public const INTRO_ANIMATION_ENABLED = 'intro_animation_enabled';

    public function get(string $key): int|bool
    {
        $defaults = [
            self::DOWNLOAD_COST => 1,
            self::ROBLOX_UPLOAD_COST => 2,
            self::REGISTRATION_BONUS => 100,
            self::ALLOW_NEGATIVE_BALANCE => false,
            self::REFUND_FAILED_UPLOAD => true,
            self::INTRO_ANIMATION_ENABLED => true,
        ];

        $value = CreditSetting::query()->where('key', $key)->value('value');
        $value ??= $defaults[$key] ?? 0;

        if (in_array($key, [self::ALLOW_NEGATIVE_BALANCE, self::REFUND_FAILED_UPLOAD, self::INTRO_ANIMATION_ENABLED], true)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (int) $value;
    }

    public function hasEnoughCredits(User $user, int $cost): bool
    {
        return $this->get(self::ALLOW_NEGATIVE_BALANCE) || $user->credits_balance >= $cost;
    }

    public function deduct(User $user, int $cost, string $action, ?Model $subject = null, array $metadata = []): CreditTransaction
    {
        if (! $this->hasEnoughCredits($user->fresh(), $cost)) {
            throw new RuntimeException("Insufficient Credits\n\nYou don't have enough credits to perform this action.\n\nPlease purchase additional credits or contact the administrator.");
        }

        return $this->change($user, -abs($cost), $action, 'success', $subject, $metadata);
    }

    public function refund(User $user, int $amount, string $action, ?Model $subject = null, array $metadata = []): CreditTransaction
    {
        return $this->change($user, abs($amount), $action, 'refunded', $subject, $metadata);
    }

    public function grant(User $user, int $amount, string $action, string $status = 'success', ?Model $subject = null, array $metadata = []): CreditTransaction
    {
        return $this->change($user, abs($amount), $action, $status, $subject, $metadata);
    }

    private function change(User $user, int $amount, string $action, string $status, ?Model $subject = null, array $metadata = []): CreditTransaction
    {
        return DB::transaction(function () use ($user, $amount, $action, $status, $subject, $metadata): CreditTransaction {
            $locked = User::query()->lockForUpdate()->findOrFail($user->id);
            $locked->forceFill(['credits_balance' => $locked->credits_balance + $amount])->save();

            return CreditTransaction::query()->create([
                'user_id' => $locked->id,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'action' => $action,
                'amount' => $amount,
                'balance_after' => $locked->credits_balance,
                'status' => $status,
                'metadata' => $metadata,
            ]);
        });
    }
}
