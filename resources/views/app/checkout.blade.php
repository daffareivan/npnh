@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <x-wx.section-card>
            <p class="text-sm uppercase tracking-[0.22em] text-white/50">{{ __('payment.checkout') }}</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">{{ $order->plan->name }} {{ __('payment.plan') }}</h1>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                    <p class="text-sm text-[#A3A3A3]">{{ __('payment.order_id') }}</p>
                    <p class="mt-2 font-semibold text-white">{{ $order->order_number }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                    <p class="text-sm text-[#A3A3A3]">{{ __('payment.amount') }}</p>
                    <p class="mt-2 font-semibold text-white">Rp {{ number_format($order->amount, 0, ',', '.') }}</p>
                </div>
            </div>
            <p class="mt-6 text-sm leading-6 text-[#A3A3A3]">{{ __('payment.waiting_gateway') }}</p>
            <a href="{{ $paymentUrl }}" class="wx-btn-primary mt-6 flex w-full items-center justify-center px-5 py-3 sm:inline-flex sm:w-auto">{{ __('payment.pay_with_midtrans') }}</a>
        </x-wx.section-card>
    </section>
@endsection
