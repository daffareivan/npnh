<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Review;
use App\Models\ReviewComment;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunityAdminController extends Controller
{
    public function reviews(): View
    {
        return view('pages.admin.community.reviews', [
            'title' => 'Community Reviews',
            'reviews' => Review::query()->with('user.badges')->latest()->paginate(20),
        ]);
    }

    public function updateReview(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,pending,rejected,hidden,deleted'],
            'is_pinned' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $review->update([
            'status' => $data['status'],
            'is_pinned' => $request->boolean('is_pinned'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return back()->with('status', 'Review updated.');
    }

    public function comments(): View
    {
        return view('pages.admin.community.comments', [
            'title' => 'Community Comments',
            'comments' => ReviewComment::query()->with(['user.badges', 'review'])->latest()->paginate(30),
        ]);
    }

    public function updateComment(Request $request, ReviewComment $comment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,pending,hidden,deleted'],
            'is_locked' => ['nullable', 'boolean'],
        ]);

        $comment->update([
            'status' => $data['status'],
            'is_locked' => $request->boolean('is_locked'),
        ]);

        return back()->with('status', 'Comment updated.');
    }

    public function reports(): View
    {
        return view('pages.admin.community.reports', [
            'title' => 'Community Reports',
        ]);
    }

    public function badges(): View
    {
        return view('pages.admin.community.badges', [
            'title' => 'Badges',
            'badges' => Badge::query()->orderByDesc('priority')->get(),
            'users' => User::query()->orderBy('name')->limit(200)->get(),
        ]);
    }

    public function toggleBan(User $user): RedirectResponse
    {
        $user->forceFill([
            'community_banned_at' => $user->community_banned_at ? null : now(),
        ])->save();

        return back()->with('status', $user->community_banned_at ? 'User banned from community.' : 'Community ban removed.');
    }

    public function storeBadge(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:60', 'unique:badges,slug'],
            'icon' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:50'],
            'priority' => ['required', 'integer', 'min:0'],
            'visibility' => ['required', 'in:public,hidden'],
            'auto_assign_rule' => ['nullable', 'string', 'max:100'],
        ]);

        Badge::query()->create([
            ...$data,
            'slug' => $data['slug'] ?: Str::slug($data['name']),
            'is_system' => false,
        ]);

        return back()->with('status', 'Badge created.');
    }

    public function assignBadge(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'badge_id' => ['required', 'exists:badges,id'],
        ]);

        $user = User::query()->findOrFail($data['user_id']);
        $user->badges()->syncWithoutDetaching([$data['badge_id'] => ['assigned_at' => now()]]);

        return back()->with('status', 'Badge assigned.');
    }
}
