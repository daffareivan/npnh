<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AudioFile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminConversionController extends Controller
{
    public function history(Request $request): View
    {
        return view('pages.admin.history', [
            'title' => __('pages.conversion_management'),
            'files' => AudioFile::query()
                ->with(['user', 'preset'])
                ->when($request->string('search')->toString(), function ($query, $search): void {
                    $query->where(fn ($q) => $q->where('original_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")));
                })
                ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function queue(): View
    {
        return view('pages.admin.queue', [
            'title' => __('pages.queue_monitor'),
            'waiting' => DB::table('jobs')->count(),
            'failed' => DB::table('failed_jobs')->count(),
        ]);
    }

    public function analytics(): View
    {
        return view('pages.admin.analytics', [
            'title' => __('pages.analytics'),
            'topUsers' => AudioFile::query()->select('user_id')->selectRaw('COUNT(*) as total')->with('user')->groupBy('user_id')->orderByDesc('total')->limit(10)->get(),
            'topPresets' => AudioFile::query()->select('speed')->selectRaw('COUNT(*) as total')->groupBy('speed')->orderByDesc('total')->get(),
        ]);
    }

    public function activity(Request $request): View
    {
        return view('pages.admin.activity', [
            'title' => __('pages.activity_log'),
            'logs' => ActivityLog::query()
                ->with('user')
                ->when($request->string('event')->toString(), fn ($query, $event) => $query->where('event', 'like', "%{$event}%"))
                ->when($request->string('user')->toString(), function ($query, $user): void {
                    $query->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$user}%")->orWhere('email', 'like', "%{$user}%"));
                })
                ->when($request->date('date'), fn ($query, $date) => $query->whereDate('created_at', $date))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }
}
