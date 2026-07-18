<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditSetting;
use App\Models\CreditTransaction;
use App\Services\CreditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreditSettingsController extends Controller
{
    public function edit(CreditService $credits): View
    {
        return view('pages.admin.credit-settings', [
            'title' => __('pages.credit_settings'),
            'settings' => [
                CreditService::REGISTRATION_BONUS => $credits->get(CreditService::REGISTRATION_BONUS),
                CreditService::DOWNLOAD_COST => $credits->get(CreditService::DOWNLOAD_COST),
                CreditService::ROBLOX_UPLOAD_COST => $credits->get(CreditService::ROBLOX_UPLOAD_COST),
                CreditService::ALLOW_NEGATIVE_BALANCE => $credits->get(CreditService::ALLOW_NEGATIVE_BALANCE),
                CreditService::REFUND_FAILED_UPLOAD => $credits->get(CreditService::REFUND_FAILED_UPLOAD),
            ],
            'transactions' => CreditTransaction::query()->with('user')->latest()->limit(25)->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            CreditService::REGISTRATION_BONUS => ['required', 'integer', 'min:0'],
            CreditService::DOWNLOAD_COST => ['required', 'integer', 'min:0'],
            CreditService::ROBLOX_UPLOAD_COST => ['required', 'integer', 'min:0'],
            CreditService::ALLOW_NEGATIVE_BALANCE => ['nullable', 'boolean'],
            CreditService::REFUND_FAILED_UPLOAD => ['nullable', 'boolean'],
        ]);

        foreach ([CreditService::ALLOW_NEGATIVE_BALANCE, CreditService::REFUND_FAILED_UPLOAD] as $booleanKey) {
            $data[$booleanKey] = $request->boolean($booleanKey) ? '1' : '0';
        }

        foreach ($data as $key => $value) {
            CreditSetting::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        return back()->with('status', 'Credit settings updated.');
    }
}
