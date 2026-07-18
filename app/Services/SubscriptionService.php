<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(private readonly CreditService $credits) {}

    public function ensureFreeSubscription(User $user): Subscription
    {
        $active = $user->activeSubscription()->with('plan')->first();
        if ($active) {
            return $active;
        }

        $free = Plan::query()->where('slug', 'free')->firstOrFail();

        return Subscription::query()->create([
            'user_id' => $user->id,
            'plan_id' => $free->id,
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function currentPlan(User $user): Plan
    {
        return $this->ensureFreeSubscription($user)->loadMissing('plan')->plan;
    }

    public function activatePlan(User $user, Plan $plan, string $source = 'subscription', ?string $customPlanName = null): Subscription
    {
        return DB::transaction(function () use ($user, $plan, $source, $customPlanName): Subscription {
            Subscription::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive', 'updated_at' => now()]);

            $subscription = Subscription::query()->create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'custom_plan_name' => $plan->is_custom ? $customPlanName : null,
                'status' => 'active',
                'started_at' => now(),
                'expired_at' => null,
            ]);

            if (! $plan->is_custom && $plan->credits !== null && $plan->credits > 0) {
                $this->credits->grant($user, $plan->credits, 'Plan Credits: '.$plan->name, 'success', $subscription, [
                    'plan' => $plan->slug,
                    'source' => $source,
                ]);
            }

            app(CommunityService::class)->syncMembershipBadge($user->fresh(['activeSubscription.plan']));

            return $subscription;
        });
    }
}
