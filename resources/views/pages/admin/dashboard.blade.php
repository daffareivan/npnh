@extends('layouts.app')

@section('content')
    @php
        $money = fn (int $value) => 'Rp '.number_format($value, 0, ',', '.');
        $statusClass = fn (string $status) => match ($status) {
            'ok' => 'bg-emerald-400/12 text-emerald-200 border-emerald-400/25',
            'warning' => 'bg-amber-400/12 text-amber-200 border-amber-400/25',
            default => 'bg-rose-400/12 text-rose-200 border-rose-400/25',
        };
    @endphp

    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-white/45">{{ __('admin.dashboard_eyebrow') }}</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-[-0.04em] text-white">{{ __('admin.dashboard_title') }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-[#A3A3A3]">{{ __('admin.dashboard_subtitle') }}</p>
        </div>
        <div class="flex flex-wrap gap-2 text-xs text-[#A3A3A3]">
            <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-2">{{ __('admin.cache_badge', ['seconds' => '45s']) }}</span>
            <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-2">{{ now()->format('M d, Y H:i') }}</span>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-5">
        @foreach($dashboard['kpis'] as $kpi)
            <article class="group rounded-[24px] border border-white/[0.07] bg-[#0b0b0d] p-5 shadow-[0_20px_80px_rgba(0,0,0,.32)] transition duration-300 hover:-translate-y-1 hover:border-white/20 hover:shadow-[0_30px_110px_rgba(139,92,246,.12)]">
                <div class="flex items-center justify-between">
                    <span class="grid size-11 place-items-center rounded-2xl border border-white/10 bg-white/[0.04] text-white">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5"/><path d="M8 19V9"/><path d="M12 19V3"/><path d="M16 19v-7"/><path d="M20 19V8"/></svg>
                    </span>
                    <span class="rounded-full border px-2.5 py-1 text-[11px] {{ $kpi['tone'] === 'positive' ? 'border-emerald-400/20 bg-emerald-400/10 text-emerald-200' : ($kpi['tone'] === 'warning' ? 'border-amber-400/20 bg-amber-400/10 text-amber-200' : 'border-white/10 bg-white/[0.04] text-[#A3A3A3]') }}">{{ ucfirst($kpi['tone']) }}</span>
                </div>
                <p class="mt-6 text-sm text-[#A3A3A3]">{{ $kpi['label'] }}</p>
                <p class="mt-2 text-3xl font-semibold tracking-[-0.04em] text-white">{{ is_numeric($kpi['value']) ? number_format($kpi['value']) : $kpi['value'] }}</p>
                <p class="mt-2 text-xs leading-5 text-[#7A7A7A]">{{ $kpi['meta'] }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.15fr_.85fr_420px]">
        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_20px_90px_rgba(0,0,0,.30)]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[#A3A3A3]">{{ __('admin.total_revenue') }}</p>
                    <h2 class="mt-2 text-3xl font-semibold text-white">{{ $money($dashboard['financial']['revenue']['month']) }}</h2>
                </div>
                <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs text-[#A3A3A3]">{{ __('admin.this_month') }}</span>
            </div>
            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                @foreach(['today' => __('admin.today'), 'month' => __('admin.month'), 'year' => __('admin.year')] as $key => $label)
                    <div class="rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                        <p class="text-xs text-[#A3A3A3]">{{ $label }}</p>
                        <p class="mt-2 text-lg font-semibold text-white">{{ $money($dashboard['financial']['revenue'][$key]) }}</p>
                    </div>
                @endforeach
            </div>
            <div id="revenueChart" class="mt-6 min-h-72"></div>
        </section>

        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_20px_90px_rgba(0,0,0,.30)]">
            <p class="text-sm text-[#A3A3A3]">{{ __('admin.credits_analytics') }}</p>
            <h2 class="mt-2 text-3xl font-semibold text-white">{{ number_format($dashboard['financial']['credits']['remaining']) }}</h2>
            <p class="mt-1 text-xs text-[#7A7A7A]">{{ __('admin.credits_remaining_all_users') }}</p>
            <div class="mt-6 grid gap-3">
                @foreach(['sold' => __('admin.credits_sold'), 'used' => __('admin.credits_used'), 'refunded' => __('admin.refund')] as $key => $label)
                    <div class="flex items-center justify-between rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                        <span class="text-sm text-[#A3A3A3]">{{ $label }}</span>
                        <span class="font-semibold text-white">{{ number_format($dashboard['financial']['credits'][$key]) }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_20px_90px_rgba(0,0,0,.30)]">
            <p class="text-sm text-[#A3A3A3]">{{ __('admin.subscription_mix') }}</p>
            <div id="subscriptionChart" class="mt-3 min-h-72"></div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_20px_90px_rgba(0,0,0,.30)]">
            <p class="text-sm text-[#A3A3A3]">{{ __('admin.conversion_analytics') }}</p>
            <div id="conversionChart" class="mt-4 min-h-72"></div>
        </section>
        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_20px_90px_rgba(0,0,0,.30)]">
            <p class="text-sm text-[#A3A3A3]">{{ __('admin.user_growth') }}</p>
            <div id="userChart" class="mt-4 min-h-72"></div>
        </section>
        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_20px_90px_rgba(0,0,0,.30)]">
            <p class="text-sm text-[#A3A3A3]">{{ __('admin.popular_speed') }}</p>
            <div id="speedChart" class="mt-4 min-h-72"></div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6">
            <h2 class="text-lg font-semibold text-white">{{ __('admin.top_users') }}</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.16em] text-[#6B7280]">
                        <tr><th class="py-3">{{ __('admin.user') }}</th><th>{{ __('admin.plan') }}</th><th>{{ __('common.credits') }}</th><th>{{ __('admin.downloads') }}</th><th>{{ __('admin.conversions') }}</th><th>{{ __('admin.roblox') }}</th><th>{{ __('admin.last_login') }}</th></tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.06]">
                        @forelse($dashboard['tables']['topUsers'] as $user)
                            <tr class="text-[#A3A3A3]">
                                <td class="py-4 pr-4"><div class="flex items-center gap-3"><div class="grid size-9 place-items-center rounded-full bg-white/10 text-xs text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</div><div><p class="font-medium text-white">{{ $user->name }}</p><p class="text-xs">{{ $user->email }}</p></div></div></td>
                                <td>{{ $user->activeSubscription?->plan?->name ?? 'Free' }}</td>
                                <td class="text-white">{{ number_format($user->credits_balance) }}</td>
                                <td>{{ number_format($user->downloads_count) }}</td>
                                <td>{{ number_format($user->conversions_count) }}</td>
                                <td>{{ number_format($user->roblox_uploads_count) }}</td>
                                <td>{{ $user->last_login_at?->diffForHumans() ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-10 text-center text-[#A3A3A3]">{{ __('admin.no_users') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6">
            <h2 class="text-lg font-semibold text-white">{{ __('admin.latest_conversions') }}</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.16em] text-[#6B7280]">
                        <tr><th class="py-3">{{ __('admin.user') }}</th><th>{{ __('admin.file') }}</th><th>{{ __('admin.speed') }}</th><th>{{ __('admin.duration') }}</th><th>{{ __('common.download') }}</th><th>{{ __('admin.roblox') }}</th><th>{{ __('common.status') }}</th><th>{{ __('common.created_at') }}</th></tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.06]">
                        @forelse($dashboard['tables']['latestConversions'] as $file)
                            <tr class="text-[#A3A3A3]">
                                <td class="py-4 pr-4 text-white">{{ $file->user?->name ?? '-' }}</td>
                                <td>{{ $file->original_name }}</td>
                                <td>{{ $file->speed }}x</td>
                                <td>{{ $file->duration ? number_format((float) $file->duration, 1).'s' : '-' }}</td>
                                <td>{{ $file->output_path ? __('admin.ready') : '-' }}</td>
                                <td>{{ $file->roblox_asset_id ? __('admin.uploaded') : ucfirst($file->roblox_status ?? '-') }}</td>
                                <td><span class="rounded-full border border-white/10 bg-white/[0.04] px-2 py-1 text-xs">{{ $file->status->value }}</span></td>
                                <td>{{ $file->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-10 text-center text-[#A3A3A3]">{{ __('admin.no_conversions') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_420px]">
        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6">
            <h2 class="text-lg font-semibold text-white">{{ __('admin.recent_payments') }}</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.16em] text-[#6B7280]">
                        <tr><th class="py-3">{{ __('admin.invoice') }}</th><th>{{ __('admin.user') }}</th><th>{{ __('admin.plan') }}</th><th>{{ __('common.credits') }}</th><th>{{ __('admin.amount') }}</th><th>{{ __('admin.method') }}</th><th>{{ __('common.status') }}</th><th>{{ __('admin.date') }}</th></tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.06]">
                        @forelse($dashboard['tables']['recentPayments'] as $order)
                            <tr class="text-[#A3A3A3]">
                                <td class="py-4 pr-4 text-white">{{ $order->order_number }}</td>
                                <td>{{ $order->user?->email ?? '-' }}</td>
                                <td>{{ $order->plan?->name ?? '-' }}</td>
                                <td>{{ $order->plan?->credits ?? '-' }}</td>
                                <td>{{ $money($order->amount) }}</td>
                                <td>{{ $order->payment_method ?? 'manual' }}</td>
                                <td>{{ ucfirst($order->payment_status) }}</td>
                                <td>{{ ($order->paid_at ?? $order->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-10 text-center text-[#A3A3A3]">{{ __('admin.no_payments') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6">
            <h2 class="text-lg font-semibold text-white">{{ __('admin.reviews') }}</h2>
            <div class="mt-5 grid grid-cols-2 gap-3">
                @foreach(['total' => __('admin.total'), 'pending' => __('common.pending'), 'approved' => __('admin.approved'), 'reported' => __('admin.reported')] as $key => $label)
                    <div class="rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                        <p class="text-xs text-[#A3A3A3]">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($dashboard['reviews'][$key]) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                <p class="text-xs text-[#A3A3A3]">{{ __('admin.average_rating') }}</p>
                <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($dashboard['reviews']['average'], 1) }}</p>
            </div>
            @if($dashboard['reviews']['latest'])
                <div class="mt-4 rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                    <p class="text-sm font-semibold text-white">{{ $dashboard['reviews']['latest']->title }}</p>
                    <p class="mt-2 line-clamp-3 text-sm text-[#A3A3A3]">{{ $dashboard['reviews']['latest']->content }}</p>
                </div>
            @endif
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[.9fr_1.1fr]">
        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6">
            <h2 class="text-lg font-semibold text-white">{{ __('admin.notifications') }}</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach($dashboard['notifications'] as $key => $value)
                    <div class="rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-[#6B7280]">{{ str_replace('_', ' ', $key) }}</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($value) }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6">
            <h2 class="text-lg font-semibold text-white">{{ __('admin.recent_activity') }}</h2>
            <div class="mt-5 space-y-3">
                @forelse($dashboard['activity'] as $activity)
                    <div class="flex gap-3 rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                        <span class="mt-1 size-2 rounded-full bg-white"></span>
                        <div>
                            <p class="font-medium text-white">{{ $activity->event }}</p>
                            <p class="mt-1 text-xs text-[#A3A3A3]">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="rounded-2xl border border-white/[0.06] bg-black/20 p-6 text-center text-sm text-[#A3A3A3]">{{ __('admin.no_activity') }}</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_420px]">
        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6">
            <h2 class="text-lg font-semibold text-white">{{ __('admin.system_health') }}</h2>
            <div class="mt-5 grid gap-3 md:grid-cols-3">
                @foreach($dashboard['system'] as $item)
                    <div class="rounded-2xl border p-4 {{ $statusClass($item['status']) }}">
                        <div class="flex items-center justify-between">
                            <p class="font-medium">{{ $item['label'] }}</p>
                            <span class="size-2 rounded-full bg-current"></span>
                        </div>
                        <p class="mt-2 text-xs opacity-80">{{ $item['detail'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-[28px] border border-white/[0.07] bg-[#0b0b0d] p-6">
            <h2 class="text-lg font-semibold text-white">{{ __('admin.quick_actions') }}</h2>
            <div class="mt-5 grid grid-cols-2 gap-3">
                @foreach([
                    [__('ui.manage_users'), route('admin.users.index')],
                    [__('admin.manage_plans'), route('admin.subscription.plans')],
                    [__('admin.manage_credits'), route('admin.credit-settings.edit')],
                    [__('admin.manage_reviews'), route('admin.content.homepage-reviews')],
                    [__('ui.open_converter'), route('app.converter')],
                    [__('admin.system_settings'), route('admin.app-settings.edit')],
                ] as [$label, $href])
                    <a href="{{ $href }}" class="rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 text-center text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:border-white/25 hover:bg-white/[0.07]">{{ $label }}</a>
                @endforeach
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const css = getComputedStyle(document.documentElement);
            const token = (name) => css.getPropertyValue(name).trim();
            const chartText = token('--chart-axis') || '#6B7280';
            const grid = token('--chart-grid') || '#E5E7EB';
            const primary = token('--primary') || '#8B5CF6';
            const warning = token('--warning') || '#F59E0B';
            const isDark = document.documentElement.classList.contains('dark');
            const base = {
                chart: { toolbar: { show: false }, foreColor: chartText, background: 'transparent' },
                grid: { borderColor: grid },
                theme: { mode: isDark ? 'dark' : 'light' },
                stroke: { curve: 'smooth', width: 3 },
                dataLabels: { enabled: false },
            };

            new ApexCharts(document.querySelector('#conversionChart'), {
                ...base,
                chart: { ...base.chart, type: 'line', height: 280 },
                series: [{ name: 'Conversions', data: @json($dashboard['charts']['conversions']['values']) }],
                xaxis: { categories: @json($dashboard['charts']['conversions']['labels']) },
                colors: [primary],
            }).render();

            new ApexCharts(document.querySelector('#revenueChart'), {
                ...base,
                chart: { ...base.chart, type: 'area', height: 300 },
                series: [{ name: 'Revenue', data: @json($dashboard['charts']['revenue']['values']) }],
                xaxis: { categories: @json($dashboard['charts']['revenue']['labels']) },
                colors: [primary],
                fill: { type: 'gradient', gradient: { opacityFrom: .35, opacityTo: .02 } },
            }).render();

            new ApexCharts(document.querySelector('#userChart'), {
                ...base,
                chart: { ...base.chart, type: 'line', height: 280 },
                series: [{ name: 'Users', data: @json($dashboard['charts']['users']['values']) }],
                xaxis: { categories: @json($dashboard['charts']['users']['labels']) },
                colors: ['#60A5FA'],
            }).render();

            new ApexCharts(document.querySelector('#speedChart'), {
                ...base,
                chart: { ...base.chart, type: 'bar', height: 280 },
                series: [{ name: 'Conversions', data: @json($dashboard['charts']['speeds']->pluck('value')) }],
                xaxis: { categories: @json($dashboard['charts']['speeds']->pluck('label')) },
                colors: [warning],
                plotOptions: { bar: { borderRadius: 8 } },
            }).render();

            new ApexCharts(document.querySelector('#subscriptionChart'), {
                chart: { type: 'donut', height: 280, background: 'transparent', foreColor: chartText },
                series: @json($dashboard['financial']['subscriptions']->pluck('value')),
                labels: @json($dashboard['financial']['subscriptions']->pluck('label')),
                colors: ['#6B7280', '#60A5FA', '#C084FC', '#FBBF24'],
                stroke: { colors: [token('--card') || '#ffffff'] },
                legend: { position: 'bottom' },
            }).render();
        });
    </script>
@endsection
