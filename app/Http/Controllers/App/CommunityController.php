<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Review;
use App\Models\ReviewComment;
use App\Services\CommunityService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    public function reviews(Request $request, CommunityService $community): View
    {
        $community->syncMembershipBadge($request->user()->load('activeSubscription.plan'));

        $reviews = Review::query()
            ->approved()
            ->with(['user.badges', 'comments'])
            ->withExists(['helpfuls as helped_by_user' => fn ($query) => $query->where('user_id', $request->user()->id)])
            ->when($request->string('rating')->toString(), fn ($query, $rating) => $query->where('rating', (int) $rating))
            ->when($request->string('badge')->toString(), function ($query, $badge): void {
                $query->whereHas('user.badges', fn ($badgeQuery) => $badgeQuery->where('slug', $badge));
            })
            ->when($request->string('sort')->toString(), function ($query, $sort): void {
                match ($sort) {
                    'oldest' => $query->oldest(),
                    'highest' => $query->orderByDesc('rating')->latest(),
                    'lowest' => $query->orderBy('rating')->latest(),
                    'helpful' => $query->orderByDesc('helpful_count')->latest(),
                    'premium' => $query->whereHas('user.badges', fn ($badgeQuery) => $badgeQuery->where('slug', 'premium'))->latest(),
                    'enterprise' => $query->whereHas('user.badges', fn ($badgeQuery) => $badgeQuery->where('slug', 'enterprise'))->latest(),
                    default => $query->latest(),
                };
            }, fn ($query) => $query->orderByDesc('is_pinned')->latest())
            ->paginate(8)
            ->withQueryString();

        return view('app.community.reviews', [
            'title' => 'Community Reviews',
            'reviews' => $reviews,
            'summary' => $community->summary(),
            'myReview' => Review::query()->where('user_id', $request->user()->id)->first(),
            'badges' => Badge::query()->visible()->orderByDesc('priority')->get(),
            'userStats' => fn ($user) => $community->userStats($user),
        ]);
    }

    public function storeReview(Request $request): RedirectResponse
    {
        abort_if($request->user()->community_banned_at, 403, 'Your community access is currently restricted.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['required', 'string', 'max:120'],
            'content' => ['required', 'string', 'max:3000'],
            'screenshot' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('screenshot')) {
            $data['screenshot_path'] = $request->file('screenshot')->store('review-screenshots', 'public');
        }

        Review::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'rating' => $data['rating'],
                'title' => $data['title'],
                'content' => $data['content'],
                'screenshot_path' => $data['screenshot_path'] ?? Review::query()->where('user_id', $request->user()->id)->value('screenshot_path'),
                'status' => 'approved',
            ]
        );

        return back()->with('status', 'Review published.');
    }

    public function helpful(Request $request, Review $review, CommunityService $community): RedirectResponse
    {
        abort_if($review->status !== 'approved', 404);
        abort_if($request->user()->community_banned_at, 403, 'Your community access is currently restricted.');

        $community->toggleHelpful($review, $request->user());

        return back()->with('status', 'Helpful preference updated.');
    }

    public function storeComment(Request $request, Review $review): RedirectResponse
    {
        abort_if($review->status !== 'approved', 404);
        abort_if($request->user()->community_banned_at, 403, 'Your community access is currently restricted.');

        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:review_comments,id'],
            'content' => ['required', 'string', 'max:1500'],
        ]);

        $parentId = $data['parent_id'] ?? null;
        if ($parentId) {
            $parent = ReviewComment::query()->where('review_id', $review->id)->findOrFail($parentId);
            abort_if($this->commentDepth($parent) >= 3 || $parent->is_locked, 422, 'Reply depth limit reached.');
        }

        ReviewComment::query()->create([
            'review_id' => $review->id,
            'parent_id' => $parentId,
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'status' => 'approved',
        ]);

        return back()->with('status', 'Comment posted.');
    }

    public function myReviews(Request $request): View
    {
        return view('app.community.my-reviews', [
            'title' => 'My Reviews',
            'review' => Review::query()->where('user_id', $request->user()->id)->with('comments')->first(),
        ]);
    }

    public function leaderboard(CommunityService $community): View
    {
        return view('app.community.leaderboard', [
            'title' => 'Leaderboard',
            'leaderboard' => $community->leaderboard(),
        ]);
    }

    private function commentDepth(ReviewComment $comment): int
    {
        $depth = 1;
        while ($comment->parent) {
            $depth++;
            $comment = $comment->parent;
        }

        return $depth;
    }
}
