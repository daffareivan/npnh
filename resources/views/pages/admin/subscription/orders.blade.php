@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Orders" />

    @if(session('status'))
        <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    <x-common.component-card title="Subscription Orders">
        <div class="overflow-x-auto">
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
        <div class="mt-5">{{ $orders->links() }}</div>
    </x-common.component-card>
@endsection
