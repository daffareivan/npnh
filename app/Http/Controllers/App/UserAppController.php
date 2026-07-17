<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AudioFile;
use App\Models\ConversionPreset;
use App\Services\CreditService;
use App\Services\CommunityService;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserAppController extends Controller
{
    public function dashboard(Request $request, SubscriptionService $subscriptions, CommunityService $community): View
    {
        $query = AudioFile::query()->where('user_id', $request->user()->id);
        $currentPlan = $subscriptions->currentPlan($request->user());
        $community->syncMembershipBadge($request->user()->fresh(['activeSubscription.plan']));
        $request->user()->load('badges');

        return view('app.dashboard', [
            'title' => 'NPNHCREATIVE',
            'stats' => [
                'total' => (clone $query)->count(),
                'today' => (clone $query)->whereDate('created_at', today())->count(),
                'month' => (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'storage' => (clone $query)->sum('output_size') + (clone $query)->sum('original_size'),
            ],
            'recent' => (clone $query)->latest()->limit(6)->get(),
            'presets' => ConversionPreset::query()->orderBy('speed')->get(),
            'defaultPreset' => ConversionPreset::query()->where('is_default', true)->first(),
            'robloxAccount' => $request->user()->robloxAccount,
            'creditBalance' => $request->user()->credits_balance,
            'currentPlan' => $currentPlan,
            'planCredits' => $currentPlan->credits,
            'membershipBadge' => $request->user()->badges->first(),
            'downloadCost' => app(CreditService::class)->get(CreditService::DOWNLOAD_COST),
            'robloxUploadCost' => app(CreditService::class)->get(CreditService::ROBLOX_UPLOAD_COST),
        ]);
    }

    public function converter(Request $request): View
    {
        return view('app.converter', [
            'title' => 'Converter',
            'presets' => ConversionPreset::query()->orderBy('speed')->get(),
            'defaultPreset' => ConversionPreset::query()->where('is_default', true)->first(),
            'robloxAccount' => $request->user()->robloxAccount,
            'creatorHubUrl' => config('services.roblox.creator_hub_url'),
            'creditBalance' => $request->user()->credits_balance,
            'downloadCost' => app(CreditService::class)->get(CreditService::DOWNLOAD_COST),
            'robloxUploadCost' => app(CreditService::class)->get(CreditService::ROBLOX_UPLOAD_COST),
        ]);
    }

    public function history(Request $request): View
    {
        return view('app.history', [
            'title' => 'History',
            'files' => AudioFile::query()
                ->where('user_id', $request->user()->id)
                ->when($request->string('search')->toString(), fn ($query, $search) => $query->where('original_name', 'like', "%{$search}%"))
                ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function profile(Request $request): View
    {
        $query = AudioFile::query()->where('user_id', $request->user()->id);
        $request->user()->load('badges');

        return view('app.profile', [
            'title' => 'Profile',
            'robloxAccount' => $request->user()->robloxAccount,
            'creditBalance' => $request->user()->credits_balance,
            'badges' => $request->user()->badges,
            'stats' => [
                'count' => (clone $query)->count(),
                'last' => (clone $query)->latest()->first(),
                'storage' => (clone $query)->sum('output_size') + (clone $query)->sum('original_size'),
            ],
        ]);
    }
}
