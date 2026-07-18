<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function dashboard(AdminDashboardService $dashboard): View
    {
        return view('pages.admin.dashboard', [
            'title' => __('pages.admin_dashboard'),
            'dashboard' => $dashboard->data(),
        ]);
    }
}
