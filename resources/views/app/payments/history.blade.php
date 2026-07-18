@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <x-wx.section-card>
            <h1 class="text-3xl font-semibold">{{ __('payment.history_title') }}</h1>
            <form class="mt-6 grid gap-3 sm:grid-cols-[1fr_180px_auto]">
                <input name="search" value="{{ request('search') }}" class="h-11 rounded-2xl border border-white/8 bg-black/20 px-4 text-sm text-white outline-none placeholder:text-[#A3A3A3] focus:border-white/30" placeholder="{{ __('payment.search_order') }}">
                <select name="status" class="h-11 rounded-2xl border border-white/8 bg-black/20 px-4 text-sm text-white">
                    <option value="">{{ __('payment.all_status') }}</option>
                    @foreach(['pending','settlement','capture','deny','cancel','expire'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="wx-btn-secondary px-5 py-3">{{ __('payment.filter') }}</button>
            </form>
            <div class="mt-6 hidden overflow-x-auto lg:block">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.14em] text-[#6B7280]">
                        <tr>
                            @foreach(['order_id', 'provider', 'payment_type', 'amount', 'status', 'paid_at', 'created_at'] as $heading)
                                <th class="px-4 py-3">{{ __("payment.$heading") }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.06]">
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="px-4 py-4 font-semibold">{{ $transaction->order_id }}</td>
                                <td class="px-4 py-4 text-[#A3A3A3]">{{ ucfirst($transaction->provider) }}</td>
                                <td class="px-4 py-4 text-[#A3A3A3]">{{ $transaction->payment_type ?: '-' }}</td>
                                <td class="px-4 py-4">{{ $transaction->formattedAmount() }}</td>
                                <td class="px-4 py-4 text-[#A3A3A3]">{{ str_replace('_', ' ', ucfirst($transaction->transaction_status)) }}</td>
                                <td class="px-4 py-4 text-[#A3A3A3]">{{ $transaction->paid_at?->format('M d, Y H:i') ?: '-' }}</td>
                                <td class="px-4 py-4 text-[#A3A3A3]">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-10 text-center text-[#A3A3A3]">{{ __('payment.no_payments') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 grid gap-3 lg:hidden">
                @forelse($transactions as $transaction)
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-semibold">{{ $transaction->order_id }}</p>
                            <span class="shrink-0 text-sm text-[#A3A3A3]">{{ str_replace('_', ' ', ucfirst($transaction->transaction_status)) }}</span>
                        </div>
                        <p class="mt-2 text-lg font-semibold">{{ $transaction->formattedAmount() }}</p>
                        <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs text-[#A3A3A3]">
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">{{ __('payment.provider') }}</dt><dd class="mt-0.5 text-white/80">{{ ucfirst($transaction->provider) }}</dd></div>
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">{{ __('payment.payment_type') }}</dt><dd class="mt-0.5 text-white/80">{{ $transaction->payment_type ?: '-' }}</dd></div>
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">{{ __('payment.paid_at') }}</dt><dd class="mt-0.5 text-white/80">{{ $transaction->paid_at?->format('M d, Y H:i') ?: '-' }}</dd></div>
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">{{ __('payment.created_at') }}</dt><dd class="mt-0.5 text-white/80">{{ $transaction->created_at->format('M d, Y H:i') }}</dd></div>
                        </dl>
                    </div>
                @empty
                    <p class="py-10 text-center text-[#A3A3A3]">{{ __('payment.no_payments') }}</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $transactions->links() }}</div>
        </x-wx.section-card>
    </section>
@endsection
