<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Models\Order;
use App\Models\Plan;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class SubscriptionController extends Controller
{
    public function pricing(Request $request, SubscriptionService $subscriptions): View
    {
        return view('app.pricing', [
            'title' => __('pages.pricing'),
            'plans' => Plan::query()->active()->ordered()->get(),
            'currentPlan' => $subscriptions->currentPlan($request->user()),
            'creditBalance' => $request->user()->credits_balance,
            'contactUrl' => $this->contactUrl(),
        ]);
    }

    public function checkout(Request $request, Plan $plan, OrderService $orders, PaymentService $payments, SubscriptionService $subscriptions): RedirectResponse
    {
        abort_if($plan->status !== 'active', 404);

        if ($plan->is_custom) {
            return redirect()->away($this->contactUrl());
        }

        $currentPlan = $subscriptions->currentPlan($request->user());
        if ($plan->price === 0 && $currentPlan->id === $plan->id) {
            return redirect()->route('app.pricing')->with('status', 'Free plan is already active.');
        }

        $order = $orders->create($request->user(), $plan, $payments->providerName());

        if ($order->payment_status === Order::STATUS_PAID) {
            $orders->markPaid($order, 'free-plan');
            return redirect()->route('app.pricing')->with('status', 'Free plan activated.');
        }

        try {
            return redirect($payments->checkoutUrl($order));
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('app.pricing')
                ->with('status', $exception->getMessage());
        }
    }

    public function showOrder(Request $request, Order $order, PaymentService $payments): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return view('app.checkout', [
            'title' => __('pages.checkout'),
            'order' => $order->load('plan'),
            'paymentUrl' => $payments->checkoutUrl($order),
        ]);
    }

    public function confirmManualPayment(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $orders->markPaid($order, 'manual-confirmation');

        return redirect()->route('app.pricing')->with('status', 'Payment confirmed. Credits have been added.');
    }

    private function contactUrl(): string
    {
        $channel = ContactSetting::valueFor('preferred_channel', 'whatsapp');
        $value = ContactSetting::valueFor($channel, '');

        if (! $value) {
            return 'mailto:'.ContactSetting::valueFor('email', config('mail.from.address'));
        }

        return match ($channel) {
            'whatsapp' => str_starts_with($value, 'http') ? $value : 'https://wa.me/'.preg_replace('/\D+/', '', $value),
            'telegram' => str_starts_with($value, 'http') ? $value : 'https://t.me/'.ltrim($value, '@'),
            'discord' => $value,
            'email' => str_starts_with($value, 'mailto:') ? $value : 'mailto:'.$value,
            default => $value,
        };
    }
}
