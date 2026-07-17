<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ConversionStatus;
use App\Models\ActivityLog;
use App\Models\AudioFile;
use App\Models\CreditTransaction;
use App\Models\DownloadLog;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Review;
use App\Models\ReviewComment;
use App\Models\Subscription;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardRepository
{
    public function totalUsers(): int
    {
        return User::query()->count();
    }

    public function usersCreatedBetween(CarbonInterface $start, CarbonInterface $end): int
    {
        return User::query()->whereBetween('created_at', [$start, $end])->count();
    }

    public function activeUsersToday(): int
    {
        return User::query()->whereDate('last_login_at', today())->count();
    }

    public function totalConversions(): int
    {
        return AudioFile::query()->count();
    }

    public function conversionsBetween(CarbonInterface $start, CarbonInterface $end): int
    {
        return AudioFile::query()->whereBetween('created_at', [$start, $end])->count();
    }

    public function totalDownloads(): int
    {
        return DownloadLog::query()->count();
    }

    public function robloxUploadStats(): array
    {
        $success = AudioFile::query()->whereNotNull('roblox_asset_id')->count();
        $failed = AudioFile::query()->where('roblox_status', 'failed')->count();
        $total = $success + $failed;

        return [
            'success' => $success,
            'failed' => $failed,
            'rate' => $total > 0 ? round(($success / $total) * 100, 1) : 0,
        ];
    }

    public function revenueBetween(CarbonInterface $start, CarbonInterface $end): int
    {
        return (int) Order::query()
            ->where('payment_status', Order::STATUS_PAID)
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');
    }

    public function creditStats(): array
    {
        return [
            'sold' => (int) CreditTransaction::query()->where('amount', '>', 0)->whereIn('action', ['Purchase Credits', 'Plan Purchase', 'Admin Seed Credits', 'Admin Bonus'])->sum('amount'),
            'used' => abs((int) CreditTransaction::query()->where('amount', '<', 0)->sum('amount')),
            'remaining' => (int) User::query()->sum('credits_balance'),
            'refunded' => (int) CreditTransaction::query()->where('status', 'refunded')->orWhere('action', 'like', '%Refund%')->sum('amount'),
        ];
    }

    public function subscriptionBreakdown(): BaseCollection
    {
        return Plan::query()
            ->select(['id', 'name', 'slug'])
            ->withCount(['subscriptions as active_count' => fn ($query) => $query->where('status', 'active')])
            ->ordered()
            ->get()
            ->map(fn (Plan $plan): array => [
                'label' => $plan->name,
                'value' => $plan->active_count,
            ]);
    }

    public function series(string $model, int $days = 30, string $dateColumn = 'created_at', ?callable $scope = null): array
    {
        $class = match ($model) {
            'users' => User::class,
            'conversions' => AudioFile::class,
            'downloads' => DownloadLog::class,
            'revenue' => Order::class,
            default => AudioFile::class,
        };

        $labels = [];
        $values = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $query = $class::query()->whereDate($dateColumn, $date);

            if ($scope) {
                $scope($query);
            }

            $labels[] = $date->format('M d');
            $values[] = $model === 'revenue' ? (int) $query->sum('amount') : $query->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function popularSpeeds(): BaseCollection
    {
        return AudioFile::query()
            ->select('speed')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('speed')
            ->orderByDesc('total')
            ->get()
            ->map(fn (AudioFile $file): array => [
                'label' => rtrim(rtrim((string) $file->speed, '0'), '.').'x',
                'value' => (int) $file->total,
            ]);
    }

    public function topUsers(): Collection
    {
        return User::query()
            ->select(['id', 'name', 'email', 'avatar', 'avatar_path', 'credits_balance', 'last_login_at'])
            ->with(['activeSubscription.plan'])
            ->withCount(['audioFiles as conversions_count', 'creditTransactions as credits_transactions_count'])
            ->withCount(['audioFiles as roblox_uploads_count' => fn ($query) => $query->whereNotNull('roblox_asset_id')])
            ->withCount(['audioFiles as downloads_count' => fn ($query) => $query->whereHas('downloadLogs')])
            ->orderByDesc('conversions_count')
            ->limit(8)
            ->get();
    }

    public function latestConversions(): Collection
    {
        return AudioFile::query()
            ->select(['id', 'user_id', 'original_name', 'duration', 'speed', 'status', 'roblox_status', 'roblox_asset_id', 'created_at', 'output_path'])
            ->with(['user:id,name,email'])
            ->latest()
            ->limit(8)
            ->get();
    }

    public function recentPayments(): Collection
    {
        return Order::query()
            ->select(['id', 'user_id', 'plan_id', 'order_number', 'amount', 'payment_method', 'payment_status', 'paid_at', 'created_at'])
            ->with(['user:id,name,email', 'plan:id,name,credits'])
            ->latest()
            ->limit(8)
            ->get();
    }

    public function reviewStats(): array
    {
        return [
            'total' => Review::query()->count(),
            'pending' => Review::query()->where('status', 'pending')->count(),
            'approved' => Review::query()->where('status', 'approved')->count(),
            'reported' => Review::query()->where('status', 'reported')->count(),
            'average' => round((float) Review::query()->avg('rating'), 1),
            'latest' => Review::query()->with('user:id,name,email')->latest()->first(),
        ];
    }

    public function recentActivity(): Collection
    {
        return ActivityLog::query()
            ->latest()
            ->limit(12)
            ->get();
    }

    public function notificationCounts(): array
    {
        return [
            'pending_reviews' => Review::query()->where('status', 'pending')->count(),
            'pending_payments' => Order::query()->whereIn('payment_status', [Order::STATUS_PENDING, Order::STATUS_WAITING_PAYMENT])->count(),
            'pending_uploads' => AudioFile::query()->where('roblox_status', 'processing')->count(),
            'pending_comments' => ReviewComment::query()->where('status', 'pending')->count(),
        ];
    }

    public function systemHealth(): array
    {
        return [
            ['label' => 'Database', 'status' => $this->checkDatabase() ? 'ok' : 'down', 'detail' => config('database.default')],
            ['label' => 'Queue', 'status' => Schema::hasTable('jobs') && DB::table('jobs')->count() > 0 ? 'warning' : 'ok', 'detail' => Schema::hasTable('jobs') ? DB::table('jobs')->count().' pending' : 'not installed'],
            ['label' => 'Storage', 'status' => is_writable(storage_path('app')) ? 'ok' : 'down', 'detail' => is_writable(storage_path('app')) ? 'writable' : 'locked'],
            ['label' => 'Cache', 'status' => 'ok', 'detail' => config('cache.default')],
            ['label' => 'Mail', 'status' => config('mail.default') ? 'ok' : 'warning', 'detail' => config('mail.default') ?: 'not configured'],
            ['label' => 'Environment', 'status' => app()->environment('production') ? 'ok' : 'warning', 'detail' => app()->environment()],
        ];
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
