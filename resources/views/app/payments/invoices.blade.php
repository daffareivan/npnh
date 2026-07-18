@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <x-wx.section-card>
            <h1 class="text-3xl font-semibold">{{ __('payment.invoices_title') }}</h1>
            <form class="mt-6 grid gap-3 sm:grid-cols-[1fr_180px_auto]">
                <input name="search" value="{{ request('search') }}" class="h-11 rounded-2xl border border-white/8 bg-black/20 px-4 text-sm text-white outline-none placeholder:text-[#A3A3A3] focus:border-white/30" placeholder="{{ __('payment.search_invoice') }}">
                <select name="status" class="h-11 rounded-2xl border border-white/8 bg-black/20 px-4 text-sm text-white">
                    <option value="">{{ __('payment.all_status') }}</option>
                    @foreach([\App\Models\Invoice::STATUS_PENDING, \App\Models\Invoice::STATUS_PAID, \App\Models\Invoice::STATUS_EXPIRED, \App\Models\Invoice::STATUS_CANCELLED] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="wx-btn-secondary px-5 py-3">{{ __('payment.filter') }}</button>
            </form>
            <div class="mt-6 hidden overflow-x-auto lg:block">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.14em] text-[#6B7280]">
                        <tr>
                            @foreach(['invoice', 'amount', 'status', 'paid_at', 'created_at'] as $heading)
                                <th class="px-4 py-3">{{ __("payment.$heading") }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.06]">
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="px-4 py-4 font-semibold">{{ $invoice->invoice_number }}</td>
                                <td class="px-4 py-4">{{ $invoice->formattedTotal() }}</td>
                                <td class="px-4 py-4 text-[#A3A3A3]">{{ ucfirst($invoice->status) }}</td>
                                <td class="px-4 py-4 text-[#A3A3A3]">{{ $invoice->paid_at?->format('M d, Y H:i') ?: '-' }}</td>
                                <td class="px-4 py-4 text-[#A3A3A3]">{{ $invoice->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-[#A3A3A3]">{{ __('payment.no_invoices') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 grid gap-3 lg:hidden">
                @forelse($invoices as $invoice)
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-semibold">{{ $invoice->invoice_number }}</p>
                            <span class="shrink-0 text-sm text-[#A3A3A3]">{{ ucfirst($invoice->status) }}</span>
                        </div>
                        <p class="mt-2 text-lg font-semibold">{{ $invoice->formattedTotal() }}</p>
                        <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs text-[#A3A3A3]">
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">{{ __('payment.paid_at') }}</dt><dd class="mt-0.5 text-white/80">{{ $invoice->paid_at?->format('M d, Y H:i') ?: '-' }}</dd></div>
                            <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">{{ __('payment.created_at') }}</dt><dd class="mt-0.5 text-white/80">{{ $invoice->created_at->format('M d, Y H:i') }}</dd></div>
                        </dl>
                    </div>
                @empty
                    <p class="py-10 text-center text-[#A3A3A3]">{{ __('payment.no_invoices') }}</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $invoices->links() }}</div>
        </x-wx.section-card>
    </section>
@endsection
