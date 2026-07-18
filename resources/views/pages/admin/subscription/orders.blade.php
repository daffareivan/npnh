@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Orders" />

    @if(session('status'))
        <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    <form class="wx-card mb-5 grid gap-3 p-3 sm:grid-cols-[1fr_180px_auto]">
        <input name="search" value="{{ request('search') }}" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none placeholder:text-[#A3A3A3] focus:border-white/30" placeholder="Search order # or user">
        <select name="status" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
            <option value="">All status</option>
            @foreach([\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_WAITING_PAYMENT, \App\Models\Order::STATUS_PAID, \App\Models\Order::STATUS_EXPIRED, \App\Models\Order::STATUS_CANCELLED, \App\Models\Order::STATUS_REFUNDED] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
            @endforeach
        </select>
        <button class="wx-btn-secondary px-5 py-3">Filter</button>
    </form>

    <x-common.component-card title="Subscription Orders">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full text-sm">
                <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.14em] text-[#6B7280]">
                    <tr>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.06]">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-4 py-3 text-white">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-[#A3A3A3]">{{ $order->user?->email }}</td>
                            <td class="px-4 py-3 text-white">{{ $order->plan?->name }}</td>
                            <td class="px-4 py-3 text-white">Rp {{ number_format($order->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-[#A3A3A3]">{{ str_replace('_', ' ', ucfirst($order->payment_status)) }}</td>
                            <td class="px-4 py-3">
                                @if($order->payment_status !== \App\Models\Order::STATUS_PAID)
                                    <form method="POST" action="{{ route('admin.subscription.orders.paid', $order) }}">
                                        @csrf
                                        <button class="wx-btn-secondary px-4 py-2">Mark Paid</button>
                                    </form>
                                @else
                                    <span class="text-[#A3A3A3]">Paid {{ $order->paid_at?->format('M d, Y H:i') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-[#A3A3A3]">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-3 lg:hidden">
            @forelse($orders as $order)
                <div class="rounded-2xl border border-white/[0.08] bg-black/20 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-medium text-white">{{ $order->order_number }}</p>
                        <span class="shrink-0 text-xs text-[#A3A3A3]">{{ str_replace('_', ' ', ucfirst($order->payment_status)) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-[#A3A3A3]">{{ $order->user?->email }}</p>
                    <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs text-[#A3A3A3]">
                        <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Plan</dt><dd class="mt-0.5 text-white/80">{{ $order->plan?->name }}</dd></div>
                        <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Amount</dt><dd class="mt-0.5 text-white/80">Rp {{ number_format($order->amount, 0, ',', '.') }}</dd></div>
                    </dl>
                    <div class="mt-3 border-t border-white/[0.06] pt-3">
                        @if($order->payment_status !== \App\Models\Order::STATUS_PAID)
                            <form method="POST" action="{{ route('admin.subscription.orders.paid', $order) }}">
                                @csrf
                                <button class="wx-btn-secondary w-full px-4 py-2">Mark Paid</button>
                            </form>
                        @else
                            <span class="text-sm text-[#A3A3A3]">Paid {{ $order->paid_at?->format('M d, Y H:i') }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="py-10 text-center text-[#A3A3A3]">No orders yet.</p>
            @endforelse
        </div>
        <div class="mt-5">{{ $orders->links() }}</div>
    </x-common.component-card>
@endsection
