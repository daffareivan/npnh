<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentCreateRequest;
use App\Models\Order;
use App\Models\Plan;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(PaymentCreateRequest $request, OrderService $orders, PaymentService $payments): RedirectResponse
    {
        $plan = Plan::query()->active()->findOrFail($request->integer('plan_id'));

        if ($plan->is_custom) {
            return redirect()->route('app.pricing')->with('status', __('payment.custom_plan_contact_admin'));
        }

        $order = $orders->create($request->user(), $plan, $payments->providerName());

        if ($order->amount === 0) {
            $orders->markPaid($order, 'free-plan');

            return redirect()->route('app.pricing')->with('status', __('payment.free_plan_activated'));
        }

        return redirect()->away($payments->checkoutUrl($order));
    }

    public function success(Request $request): RedirectResponse
    {
        return redirect()->route('app.pricing')->with('status', __('payment.success_waiting_webhook'));
    }

    public function pending(Request $request): RedirectResponse
    {
        return redirect()->route('payment.history')->with('status', __('payment.pending'));
    }

    public function failed(Request $request): RedirectResponse
    {
        return redirect()->route('payment.history')->with('status', __('payment.failed'));
    }
}
