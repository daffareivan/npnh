<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AudioFile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class AdminConversionController extends Controller
{
    public function history(): View
    {
        return view('pages.admin.history', [
            'title' => 'Conversion Management',
            'files' => AudioFile::query()->with(['user', 'preset'])->latest()->paginate(10),
        ]);
    }

    public function queue(): View
    {
        return view('pages.admin.queue', [
            'title' => 'Queue Monitor',
            'waiting' => DB::table('jobs')->count(),
            'failed' => DB::table('failed_jobs')->count(),
        ]);
    }

    public function analytics(): View
    {
        return view('pages.admin.analytics', [
            'title' => 'Analytics',
            'topUsers' => AudioFile::query()->select('user_id')->selectRaw('COUNT(*) as total')->with('user')->groupBy('user_id')->orderByDesc('total')->limit(10)->get(),
            'topPresets' => AudioFile::query()->select('speed')->selectRaw('COUNT(*) as total')->groupBy('speed')->orderByDesc('total')->get(),
        ]);
    }

    public function activity(): View
    {
        return view('pages.admin.activity', [
            'title' => 'Activity Log',
            'logs' => ActivityLog::query()->latest()->paginate(20),
        ]);
    }
}
