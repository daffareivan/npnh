<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Repositories\AdminDashboardRepository;
use Illuminate\Support\Facades\Cache;

class AdminDashboardService
{
    public function __construct(private readonly AdminDashboardRepository $dashboard) {}

    public function data(): array
    {
        return Cache::remember('admin.dashboard.bi.v1', now()->addSeconds(45), function (): array {
            $thisMonthStart = now()->startOfMonth();
            $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
            $lastMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

            $currentUsers = $this->dashboard->usersCreatedBetween($thisMonthStart, now());
            $previousUsers = $this->dashboard->usersCreatedBetween($lastMonthStart, $lastMonthEnd);
            $userGrowth = $previousUsers > 0 ? round((($currentUsers - $previousUsers) / $previousUsers) * 100, 1) : ($currentUsers > 0 ? 100 : 0);

            $roblox = $this->dashboard->robloxUploadStats();
            $credits = $this->dashboard->creditStats();

            return [
                'kpis' => [
                    ['label' => 'Total Users', 'value' => $this->dashboard->totalUsers(), 'meta' => $userGrowth.'% vs last month', 'tone' => $userGrowth >= 0 ? 'positive' : 'negative', 'icon' => 'users'],
                    ['label' => 'Active Users Today', 'value' => $this->dashboard->activeUsersToday(), 'meta' => 'last_login_at today', 'tone' => 'neutral', 'icon' => 'activity'],
                    ['label' => 'Total Audio Converted', 'value' => $this->dashboard->totalConversions(), 'meta' => $this->dashboard->conversionsBetween(today(), now()).' today / '.$this->dashboard->conversionsBetween(now()->startOfWeek(), now()).' this week', 'tone' => 'neutral', 'icon' => 'audio'],
                    ['label' => 'Total Downloads', 'value' => $this->dashboard->totalDownloads(), 'meta' => 'credit-gated result usage', 'tone' => 'neutral', 'icon' => 'download'],
                    ['label' => 'Roblox Uploads', 'value' => $roblox['success'], 'meta' => $roblox['failed'].' failed / '.$roblox['rate'].'% success', 'tone' => $roblox['failed'] > 0 ? 'warning' : 'positive', 'icon' => 'cloud'],
                ],
                'financial' => [
                    'revenue' => [
                        'today' => $this->dashboard->revenueBetween(today(), now()),
                        'month' => $this->dashboard->revenueBetween(now()->startOfMonth(), now()),
                        'year' => $this->dashboard->revenueBetween(now()->startOfYear(), now()),
                        'previous_month' => $this->dashboard->revenueBetween($lastMonthStart, $lastMonthEnd),
                    ],
                    'credits' => $credits,
                    'subscriptions' => $this->dashboard->subscriptionBreakdown(),
                ],
                'charts' => [
                    'conversions' => $this->dashboard->series('conversions', 30),
                    'revenue' => $this->dashboard->series('revenue', 30, 'paid_at', fn ($query) => $query->where('payment_status', Order::STATUS_PAID)),
                    'users' => $this->dashboard->series('users', 30),
                    'speeds' => $this->dashboard->popularSpeeds(),
                ],
                'tables' => [
                    'topUsers' => $this->dashboard->topUsers(),
                    'latestConversions' => $this->dashboard->latestConversions(),
                    'recentPayments' => $this->dashboard->recentPayments(),
                ],
                'reviews' => $this->dashboard->reviewStats(),
                'activity' => $this->dashboard->recentActivity(),
                'notifications' => $this->dashboard->notificationCounts(),
                'system' => $this->dashboard->systemHealth(),
            ];
        });
    }
}
