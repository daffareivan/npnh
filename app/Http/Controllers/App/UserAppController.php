<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Enums\ConversionStatus;
use App\Http\Controllers\Controller;
use App\Models\AudioFile;
use App\Models\ConversionPreset;
use App\Models\DownloadLog;
use App\Services\CreditService;
use App\Services\CommunityService;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserAppController extends Controller
{
    public function dashboard(Request $request, SubscriptionService $subscriptions, CommunityService $community): View
    {
        $query = AudioFile::query()->where('user_id', $request->user()->id);
        $currentPlan = $subscriptions->currentPlan($request->user());
        $community->syncMembershipBadge($request->user()->fresh(['activeSubscription.plan']));
        $request->user()->load('badges');

        return view('app.dashboard', [
            'title' => __('pages.dashboard'),
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
            'title' => __('pages.converter'),
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
        $user = $request->user();
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        // Every download is its own timeline entry (like a transaction log), in addition
        // to the conversion entry itself, so downloading the same file repeatedly still
        // shows up as separate history rows.
        $conversions = AudioFile::query()
            ->where('user_id', $user->id)
            ->when($search, fn ($query, $search) => $query->where('original_name', 'like', "%{$search}%"))
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            // A finished conversion only earns its place in history once the user has
            // actually done something with it (downloaded or uploaded to Roblox) —
            // otherwise it clutters the list the moment the background job finishes.
            ->where(function ($query): void {
                $query->where('status', '!=', ConversionStatus::Finished->value)
                    ->orWhereHas('files', fn ($q) => $q->whereNotNull('downloaded_at')->orWhereNotNull('uploaded_at'));
            })
            ->with('files')
            ->latest()
            ->limit(500)
            ->get()
            ->map(fn (AudioFile $file) => ['type' => 'conversion', 'audio_file' => $file, 'conversion_file' => null, 'created_at' => $file->created_at]);

        $downloads = DownloadLog::query()
            ->where('user_id', $user->id)
            ->whereHas('audioFile', function ($query) use ($search, $status): void {
                $query->when($search, fn ($q, $search) => $q->where('original_name', 'like', "%{$search}%"))
                    ->when($status, fn ($q, $status) => $q->where('status', $status));
            })
            ->with(['audioFile', 'conversionFile'])
            ->latest()
            ->limit(500)
            ->get()
            ->map(fn (DownloadLog $log) => ['type' => 'download', 'audio_file' => $log->audioFile, 'conversion_file' => $log->conversionFile, 'created_at' => $log->created_at]);

        $events = $conversions->concat($downloads)->sortByDesc('created_at')->values();
        $page = (int) $request->integer('page', 1);
        $perPage = 10;

        $paginated = new LengthAwarePaginator(
            $events->forPage($page, $perPage)->values(),
            $events->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('app.history', [
            'title' => __('pages.history'),
            'events' => $paginated,
        ]);
    }

    public function profile(Request $request): View
    {
        $query = AudioFile::query()->where('user_id', $request->user()->id);
        $request->user()->load('badges');

        return view('app.profile', [
            'title' => __('pages.profile'),
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
