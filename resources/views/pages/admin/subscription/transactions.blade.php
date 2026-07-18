@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Credit Transactions" />

    <form class="wx-card mb-5 grid gap-3 p-3 sm:grid-cols-[1fr_180px_auto]">
        <input name="search" value="{{ request('search') }}" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none placeholder:text-[#A3A3A3] focus:border-white/30" placeholder="Search action or user">
        <select name="status" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
            <option value="">All status</option>
            @foreach(['success','refunded'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button class="wx-btn-secondary px-5 py-3">Filter</button>
    </form>

    <x-common.component-card title="Audit Log" desc="Every credit change is recorded here.">
        <div class="hidden overflow-x-auto lg:block">
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

        <div class="grid gap-3 lg:hidden">
            @forelse($transactions as $transaction)
                <div class="rounded-2xl border border-white/[0.08] bg-black/20 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-medium text-white">{{ $transaction->action }}</p>
                        <span class="shrink-0 text-lg font-semibold {{ $transaction->amount < 0 ? 'text-rose-200' : 'text-white' }}">{{ $transaction->amount }}</span>
                    </div>
                    <p class="mt-1 text-sm text-[#A3A3A3]">{{ $transaction->user?->email }}</p>
                    <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs text-[#A3A3A3]">
                        <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Status</dt><dd class="mt-0.5 text-white/80">{{ ucfirst($transaction->status) }}</dd></div>
                        <div><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Balance</dt><dd class="mt-0.5 text-white/80">{{ $transaction->balance_after }}</dd></div>
                        <div class="col-span-2"><dt class="uppercase tracking-[0.1em] text-[#6B7280]">Date</dt><dd class="mt-0.5 text-white/80">{{ $transaction->created_at->format('M d, Y H:i') }}</dd></div>
                    </dl>
                </div>
            @empty
                <p class="py-10 text-center text-[#A3A3A3]">No credit transactions yet.</p>
            @endforelse
        </div>
        <div class="mt-5">{{ $transactions->links() }}</div>
    </x-common.component-card>
@endsection
