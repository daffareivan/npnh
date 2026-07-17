<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AudioFile;
use App\Models\Badge;
use App\Models\DownloadLog;
use App\Models\Review;
use App\Models\ReviewHelpful;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommunityService
{
    public function syncMembershipBadge(User $user): void
    {
        $plan = $user->activeSubscription?->plan?->slug ?? 'free';
        $slug = match ($plan) {
            'standard' => 'standard',
            'premium' => 'premium',
            'custom' => 'enterprise',
            default => 'free',
        };

        $badge = Badge::query()->where('slug', $slug)->first();
        if (! $badge) {
            return;
        }

        $membershipIds = Badge::query()->whereIn('slug', ['free', 'standard', 'premium', 'enterprise'])->pluck('id');
        $user->badges()->detach($membershipIds);
        $user->badges()->syncWithoutDetaching([$badge->id => ['assigned_at' => now()]]);
    }

    public function summary(): array
    {
        $approved = Review::query()->approved();
        $count = (clone $approved)->count();
        $average = $count ? round((float) (clone $approved)->avg('rating'), 1) : 0;
        $distribution = [];

        foreach ([5, 4, 3, 2, 1] as $rating) {
            $ratingCount = (clone $approved)->where('rating', $rating)->count();
            $distribution[$rating] = [
                'count' => $ratingCount,
                'percent' => $count ? (int) round(($ratingCount / $count) * 100) : 0,
            ];
        }

        return compact('count', 'average', 'distribution');
    }

    public function toggleHelpful(Review $review, User $user): bool
    {
        return DB::transaction(function () use ($review, $user): bool {
            $existing = ReviewHelpful::query()->where('review_id', $review->id)->where('user_id', $user->id)->first();

            if ($existing) {
                $existing->delete();
                $review->decrement('helpful_count');
                return false;
            }

            ReviewHelpful::query()->create(['review_id' => $review->id, 'user_id' => $user->id]);
            $review->increment('helpful_count');

            return true;
        });
    }

    public function leaderboard(): array
    {
        return [
            'topReviewer' => User::query()->withCount('reviews')->orderByDesc('reviews_count')->limit(5)->get(),
            'mostHelpful' => User::query()
                ->select('users.*')
                ->selectSub(
                    Review::query()
                        ->selectRaw('COALESCE(SUM(helpful_count),0)')
                        ->whereColumn('reviews.user_id', 'users.id'),
                    'helpful_total'
                )
                ->orderByDesc('helpful_total')
                ->limit(5)
                ->get(),
            'mostDownloads' => User::query()->withCount('audioFiles')->orderByDesc('audio_files_count')->limit(5)->get(),
            'mostActive' => User::query()->withCount(['reviews', 'reviewComments'])->orderByDesc('review_comments_count')->orderByDesc('reviews_count')->limit(5)->get(),
            'highestRatedReview' => Review::query()->approved()->with('user.badges')->orderByDesc('rating')->orderByDesc('helpful_count')->first(),
        ];
    }

    public function userStats(User $user): array
    {
        return [
            'joined' => $user->created_at?->format('M Y'),
            'conversions' => AudioFile::query()->where('user_id', $user->id)->count(),
            'downloads' => class_exists(DownloadLog::class) ? DownloadLog::query()->where('user_id', $user->id)->count() : 0,
            'credits_used' => abs((int) $user->creditTransactions()->where('amount', '<', 0)->sum('amount')),
            'helpful_received' => (int) Review::query()->where('user_id', $user->id)->sum('helpful_count'),
        ];
    }
}
