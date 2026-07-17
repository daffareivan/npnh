<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ConversionStatus;
use App\Models\AudioFile;
use App\Models\AppSetting;
use App\Models\ConversionPreset;
use App\Models\CreditTransaction;
use App\Models\DownloadLog;
use App\Models\Plan;
use App\Models\Review;
use App\Models\User;
use App\Services\CommunityService;
use Illuminate\Contracts\View\View;
use Throwable;

class HomeController extends Controller
{
    public function __invoke(CommunityService $community): View
    {
        try {
            $presets = ConversionPreset::query()->orderBy('speed')->get();
        } catch (Throwable) {
            $presets = collect([
                ['name' => '2.3x', 'speed' => 2.3, 'amplify_db' => -4],
                ['name' => '2.5x', 'speed' => 2.5, 'amplify_db' => -6],
                ['name' => '2.7x', 'speed' => 2.7, 'amplify_db' => -8],
            ]);
        }

        $homepageReviews = Review::query()
            ->approved()
            ->with(['user.badges', 'user.activeSubscription.plan'])
            ->withCount('allComments')
            ->orderByDesc('is_featured')
            ->orderByDesc('is_pinned')
            ->orderByDesc('helpful_count')
            ->latest()
            ->limit(9)
            ->get();

        $gradients = [
            'from-[#7C3AED] to-[#2563EB]',
            'from-[#EA580C] to-[#DB2777]',
            'from-[#059669] to-[#0891B2]',
            'from-[#9333EA] to-[#F59E0B]',
        ];

        $homepageReviews->each(function (Review $review) use ($gradients): void {
            $review->setAttribute('homepage_badge_slugs', $review->user?->badges?->pluck('slug')->implode(' ') ?? '');
            $review->setAttribute('homepage_membership_badge', $review->user?->badges?->first(fn ($badge) => in_array($badge->slug, ['free', 'standard', 'premium', 'enterprise'], true)));
            $review->setAttribute('homepage_verified_badge', $review->user?->badges?->firstWhere('slug', 'verified'));
            $review->setAttribute('homepage_avatar', $review->user?->avatar ?: $review->user?->avatar_path);
            $review->setAttribute('homepage_gradient', $gradients[$review->id % count($gradients)]);
            $review->setAttribute('homepage_user_downloads', DownloadLog::query()->where('user_id', $review->user_id)->count());
            $review->setAttribute('homepage_user_conversions', AudioFile::query()->where('user_id', $review->user_id)->count());
            $review->setAttribute('homepage_user_reviews', Review::query()->where('user_id', $review->user_id)->count());
            $review->setAttribute('homepage_user_helpful_received', Review::query()->where('user_id', $review->user_id)->sum('helpful_count'));
            $review->setAttribute('homepage_user_plan', $review->user?->activeSubscription?->plan?->name ?? 'Free');
            $review->setAttribute('homepage_user_since', $review->user?->created_at?->format('M Y'));
        });

        $summary = $community->summary();
        $audioTotal = AudioFile::query()->count();
        $audioFinished = AudioFile::query()->where('status', ConversionStatus::Finished->value)->count();
        $successRate = $audioTotal > 0 ? round(($audioFinished / $audioTotal) * 100, 1) : 0;
        $averageDuration = (float) AudioFile::query()
            ->where('status', ConversionStatus::Finished->value)
            ->whereNotNull('duration')
            ->avg('duration');
        $queueWaiting = AudioFile::query()
            ->whereNotIn('status', [ConversionStatus::Finished->value, ConversionStatus::Failed->value])
            ->count();
        $queueFailed = AudioFile::query()->where('status', ConversionStatus::Failed->value)->count();

        return view('home', [
            'title' => 'NPNHCREATIVE',
            'presets' => $presets,
            'plans' => Plan::query()->active()->ordered()->get(),
            'reviewSummary' => $summary,
            'homepageReviews' => $homepageReviews,
            'reviewFilterCounts' => [
                'all' => $summary['count'],
                '5' => $summary['distribution'][5]['count'],
                '4' => $summary['distribution'][4]['count'],
                '3' => $summary['distribution'][3]['count'],
                '2' => $summary['distribution'][2]['count'],
                '1' => $summary['distribution'][1]['count'],
                'premium' => $homepageReviews->filter(fn (Review $review) => $review->user?->badges?->contains('slug', 'premium'))->count(),
                'standard' => $homepageReviews->filter(fn (Review $review) => $review->user?->badges?->contains('slug', 'standard'))->count(),
                'enterprise' => $homepageReviews->filter(fn (Review $review) => $review->user?->badges?->contains('slug', 'enterprise'))->count(),
                'verified' => $homepageReviews->filter(fn (Review $review) => $review->user?->badges?->contains('slug', 'verified'))->count(),
            ],
            'reviewSchema' => $homepageReviews->take(3)->map(fn (Review $review): array => [
                '@type' => 'Review',
                'author' => ['@type' => 'Person', 'name' => $review->user?->name],
                'name' => $review->title,
                'reviewBody' => str($review->content)->limit(220)->toString(),
                'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $review->rating, 'bestRating' => 5],
            ])->values(),
            'homepageStats' => [
                'converted' => $audioFinished,
                'users' => User::query()->count(),
                'downloads' => DownloadLog::query()->count(),
                'credits_used' => abs((int) CreditTransaction::query()->where('amount', '<', 0)->sum('amount')),
                'reviews' => $summary['count'],
                'rating' => $summary['average'],
                'success' => $successRate,
                'average_duration' => $averageDuration,
                'queue_waiting' => $queueWaiting,
                'queue_failed' => $queueFailed,
            ],
            'introAnimationEnabled' => AppSetting::boolean(AppSetting::INTRO_ANIMATION_ENABLED, true),
        ]);
    }
}
