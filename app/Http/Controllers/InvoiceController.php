<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function paymentHistory(Request $request): View
    {
        return view('app.payments.history', [
            'title' => __('payment.history_title'),
            'transactions' => PaymentTransaction::query()
                ->where('user_id', $request->user()->id)
                ->when($request->string('search')->toString(), fn ($query, $search) => $query->where('order_id', 'like', "%{$search}%"))
                ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('transaction_status', $status))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function invoices(Request $request): View
    {
        return view('app.payments.invoices', [
            'title' => __('payment.invoices_title'),
            'invoices' => Invoice::query()
                ->where('user_id', $request->user()->id)
                ->when($request->string('search')->toString(), fn ($query, $search) => $query->where('invoice_number', 'like', "%{$search}%"))
                ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }
}
