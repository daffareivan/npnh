@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="__('credits.settings_title')" />

    @if(session('status'))
        <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <x-common.component-card :title="__('credits.credit_costs')" :desc="__('credits.credit_costs_desc')">
            <form method="POST" action="{{ route('admin.credit-settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')
                @foreach([
                    \App\Services\CreditService::REGISTRATION_BONUS => __('credits.registration_bonus'),
                    \App\Services\CreditService::DOWNLOAD_COST => __('credits.download_cost'),
                    \App\Services\CreditService::ROBLOX_UPLOAD_COST => __('credits.roblox_upload_cost'),
                ] as $key => $label)
                    <label class="block">
                        <span class="mb-1.5 block text-sm text-[#A3A3A3]">{{ $label }}</span>
                        <input name="{{ $key }}" type="number" min="0" value="{{ $settings[$key] }}" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    </label>
                @endforeach

                <label class="flex items-center justify-between rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                    <span>
                        <span class="block font-medium text-white">{{ __('credits.allow_negative_balance') }}</span>
                        <span class="text-sm text-[#A3A3A3]">{{ __('credits.allow_negative_balance_help') }}</span>
                    </span>
                    <input name="{{ \App\Services\CreditService::ALLOW_NEGATIVE_BALANCE }}" type="checkbox" value="1" @checked($settings[\App\Services\CreditService::ALLOW_NEGATIVE_BALANCE])>
                </label>

                <label class="flex items-center justify-between rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                    <span>
                        <span class="block font-medium text-white">{{ __('credits.refund_failed_upload') }}</span>
                        <span class="text-sm text-[#A3A3A3]">{{ __('credits.refund_failed_upload_help') }}</span>
                    </span>
                    <input name="{{ \App\Services\CreditService::REFUND_FAILED_UPLOAD }}" type="checkbox" value="1" @checked($settings[\App\Services\CreditService::REFUND_FAILED_UPLOAD])>
                </label>

                <button class="wx-btn-primary px-5 py-3">{{ __('admin.save_settings') }}</button>
            </form>
        </x-common.component-card>

        <x-common.component-card :title="__('credits.recent_transactions')">
            <div class="hidden overflow-x-auto xl:block">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.14em] text-[#6B7280]">
                        <tr>
                            <th class="px-4 py-3">{{ __('admin.user') }}</th>
                            <th class="px-4 py-3">{{ __('common.action') }}</th>
                            <th class="px-4 py-3">{{ __('credits.credit') }}</th>
                            <th class="px-4 py-3">{{ __('common.status') }}</th>
                            <th class="px-4 py-3">{{ __('credits.balance') }}</th>
                            <th class="px-4 py-3">{{ __('admin.date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.06]">
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="px-4 py-3 text-[#A3A3A3]">{{ $transaction->user?->email }}</td>
                                <td class="px-4 py-3 text-white">{{ $transaction->action }}</td>
                                <td class="px-4 py-3 {{ $transaction->amount < 0 ? 'text-rose-200' : 'text-white' }}">{{ $transaction->amount }}</td>
                                <td class="px-4 py-3 text-[#A3A3A3]">{{ ucfirst($transaction->status) }}</td>
                                <td class="px-4 py-3 text-white">{{ $transaction->balance_after }}</td>
                                <td class="px-4 py-3 text-[#A3A3A3]">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-[#A3A3A3]">{{ __('credits.no_transactions') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="grid gap-3 xl:hidden">
                @forelse($transactions as $transaction)
                    <div class="rounded-2xl border border-white/[0.08] bg-black/20 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-medium text-white">{{ $transaction->action }}</p>
                            <span class="shrink-0 text-lg font-semibold {{ $transaction->amount < 0 ? 'text-rose-200' : 'text-white' }}">{{ $transaction->amount }}</span>
                        </div>
                        <p class="mt-1 text-sm text-[#A3A3A3]">{{ $transaction->user?->email }}</p>
                        <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs text-[#A3A3A3]">
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">{{ __('common.status') }}</dt><dd class="mt-0.5 text-white/80">{{ ucfirst($transaction->status) }}</dd></div>
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">{{ __('credits.balance') }}</dt><dd class="mt-0.5 text-white/80">{{ $transaction->balance_after }}</dd></div>
                            <div class="col-span-2"><dt class="uppercase tracking-[0.1em] text-[#6B7280]">{{ __('admin.date') }}</dt><dd class="mt-0.5 text-white/80">{{ $transaction->created_at->format('M d, Y H:i') }}</dd></div>
                        </dl>
                    </div>
                @empty
                    <p class="py-10 text-center text-[#A3A3A3]">{{ __('credits.no_transactions') }}</p>
                @endforelse
            </div>
        </x-common.component-card>
    </div>
@endsection
