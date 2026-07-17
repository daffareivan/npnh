<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Models\CreditTransaction;
use App\Models\Order;
use App\Models\Plan;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionAdminController extends Controller
{
    public function plans(): View
    {
        return view('pages.admin.subscription.plans', [
            'title' => 'Subscription Plans',
            'plans' => Plan::query()->ordered()->get(),
        ]);
    }

    public function updatePlan(Request $request, Plan $plan): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'price' => ['nullable', 'integer', 'min:0'],
            'credits' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'string'],
        ]);

        $plan->update([
            ...$data,
            'price' => $plan->is_custom ? null : $data['price'],
            'credits' => $plan->is_custom ? null : $data['credits'],
            'features' => collect(preg_split('/\r\n|\r|\n/', $data['features'] ?? ''))
                ->map(fn ($feature) => trim($feature))
                ->filter()
                ->values()
                ->all(),
        ]);

        return back()->with('status', 'Plan updated.');
    }

    public function orders(): View
    {
        return view('pages.admin.subscription.orders', [
            'title' => 'Orders',
            'orders' => Order::query()->with(['user', 'plan'])->latest()->paginate(20),
        ]);
    }

    public function markOrderPaid(Order $order, OrderService $orders): RedirectResponse
    {
        $orders->markPaid($order, 'admin-confirmation');

        return back()->with('status', 'Order marked as paid and credits added.');
    }

    public function transactions(): View
    {
        return view('pages.admin.subscription.transactions', [
            'title' => 'Credit Transactions',
            'transactions' => CreditTransaction::query()->with('user')->latest()->paginate(30),
        ]);
    }

    public function contactSettings(): View
    {
        return view('pages.admin.subscription.contact-settings', [
            'title' => 'Contact Settings',
            'settings' => ContactSetting::query()->pluck('value', 'key')->all(),
        ]);
    }

    public function updateContactSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'preferred_channel' => ['required', 'in:whatsapp,telegram,discord,email'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'discord' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            ContactSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('status', 'Contact settings updated.');
    }
}
