@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Subscription Plans" />

    @if(session('status'))
        <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 xl:grid-cols-2">
        @foreach($plans as $plan)
            <x-common.component-card :title="$plan->name" desc="Manage price, credits, status, and ordering.">
                <form method="POST" action="{{ route('admin.subscription.plans.update', $plan) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label>
                            <span class="mb-1.5 block text-sm text-[#A3A3A3]">Plan Name</span>
                            <input name="name" value="{{ $plan->name }}" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                        </label>
                        <label>
                            <span class="mb-1.5 block text-sm text-[#A3A3A3]">Status</span>
                            <select name="status" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                                <option value="active" @selected($plan->status === 'active')>Active</option>
                                <option value="inactive" @selected($plan->status === 'inactive')>Inactive</option>
                            </select>
                        </label>
                        <label>
                            <span class="mb-1.5 block text-sm text-[#A3A3A3]">Price</span>
                            <input name="price" type="number" min="0" value="{{ $plan->price }}" @disabled($plan->is_custom) class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30 disabled:opacity-50">
                        </label>
                        <label>
                            <span class="mb-1.5 block text-sm text-[#A3A3A3]">Credits</span>
                            <input name="credits" type="number" min="0" value="{{ $plan->credits }}" @disabled($plan->is_custom) class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30 disabled:opacity-50">
                        </label>
                        <label>
                            <span class="mb-1.5 block text-sm text-[#A3A3A3]">Max Uploads <span class="normal-case text-[#6B7280]">(kosongkan = unlimited)</span></span>
                            <input name="max_uploads" type="number" min="0" value="{{ $plan->max_uploads }}" placeholder="Unlimited" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                            <span class="mt-1 block text-xs text-[#6B7280]">Ditambahkan ke kuota upload user secara kumulatif setiap kali plan ini diaktifkan (tidak reset).</span>
                        </label>
                    </div>
                    <label>
                        <span class="mb-1.5 block text-sm text-[#A3A3A3]">Sort Order</span>
                        <input name="sort_order" type="number" min="0" value="{{ $plan->sort_order }}" class="h-11 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    </label>
                    <label>
                        <span class="mb-1.5 block text-sm text-[#A3A3A3]">Features</span>
                        <textarea name="features" rows="5" class="w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 py-3 text-sm text-white outline-none focus:border-white/30">{{ implode("\n", $plan->features ?? []) }}</textarea>
                    </label>
                    <button class="wx-btn-primary px-5 py-3">Save Plan</button>
                </form>
            </x-common.component-card>
        @endforeach
    </div>
@endsection
