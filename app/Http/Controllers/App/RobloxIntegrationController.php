<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\Roblox\RobloxAccountService;
use App\Services\Roblox\RobloxOAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RobloxIntegrationController extends Controller
{
    public function show(Request $request): View
    {
        return view('app.integrations.roblox', [
            'title' => 'Roblox Integration',
            'account' => $request->user()->robloxAccount,
            'creatorHubUrl' => config('services.roblox.creator_hub_url'),
        ]);
    }

    public function redirect(RobloxOAuthService $oauth): RedirectResponse
    {
        abort_unless(config('services.roblox.client_id') && config('services.roblox.client_secret'), 503, 'Roblox OAuth is not configured yet.');

        return $oauth->redirect();
    }

    public function switch(Request $request, RobloxOAuthService $oauth, RobloxAccountService $accounts): RedirectResponse
    {
        abort_unless(config('services.roblox.client_id') && config('services.roblox.client_secret'), 503, 'Roblox OAuth is not configured yet.');

        $account = $request->user()->robloxAccount;

        if ($account) {
            ActivityLog::query()->create([
                'user_id' => $request->user()->id,
                'subject_type' => $account::class,
                'subject_id' => $account->id,
                'event' => 'Switched Roblox Account',
                'properties' => ['previous_roblox_user_id' => $account->roblox_user_id],
            ]);

            $accounts->disconnect($request->user());
        }

        return $oauth->redirect(forcePrompt: true);
    }

    public function callback(Request $request, RobloxOAuthService $oauth, RobloxAccountService $accounts): RedirectResponse
    {
        abort_if($request->string('state')->toString() !== session('roblox_oauth_state'), 403, 'Invalid Roblox OAuth state.');
        $request->session()->forget('roblox_oauth_state');

        $request->validate(['code' => ['required', 'string']]);

        $account = $accounts->connect($request->user(), $oauth->exchangeCode($request->string('code')->toString()));

        ActivityLog::query()->create([
            'user_id' => $request->user()->id,
            'subject_type' => $account::class,
            'subject_id' => $account->id,
            'event' => 'Connected Roblox',
            'properties' => ['roblox_user_id' => $account->roblox_user_id],
        ]);

        return redirect()->route('app.integrations.roblox')->with('status', 'Roblox account connected successfully.');
    }

    public function disconnect(Request $request, RobloxAccountService $accounts): RedirectResponse
    {
        $account = $request->user()->robloxAccount;
        abort_unless($account, 404);

        ActivityLog::query()->create([
            'user_id' => $request->user()->id,
            'subject_type' => $account::class,
            'subject_id' => $account->id,
            'event' => 'Disconnected Roblox',
            'properties' => ['roblox_user_id' => $account->roblox_user_id],
        ]);

        $accounts->disconnect($request->user());

        return back()->with('status', 'Roblox account disconnected.');
    }
}
