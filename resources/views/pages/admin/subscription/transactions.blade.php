@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Credit Transactions" />

    <x-common.component-card title="Audit Log" desc="Every credit change is recorded here.">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.14em] text-[#6B7280]">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Credit</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Balance</th>
                        <th class="px-4 py-3">Date</th>
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
                        <tr><td colspan="6" class="px-4 py-10 text-center text-[#A3A3A3]">No credit transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $transactions->links() }}</div>
    </x-common.component-card>
@endsection
