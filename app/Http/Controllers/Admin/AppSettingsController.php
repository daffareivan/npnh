<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppSettingsController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.app-settings', [
            'title' => __('pages.app_settings'),
            'introAnimationEnabled' => AppSetting::boolean(AppSetting::INTRO_ANIMATION_ENABLED, true),
            'themeDefault' => AppSetting::valueFor(AppSetting::THEME_DEFAULT, 'system'),
            'localeDefault' => AppSetting::valueFor(AppSetting::LOCALE_DEFAULT, 'en'),
            'allowThemeSwitch' => AppSetting::boolean(AppSetting::ALLOW_THEME_SWITCH, true),
            'allowLanguageSwitch' => AppSetting::boolean(AppSetting::ALLOW_LANGUAGE_SWITCH, true),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            AppSetting::THEME_DEFAULT => ['required', 'in:light,dark,system'],
            AppSetting::LOCALE_DEFAULT => ['required', 'in:id,en'],
        ]);

        AppSetting::query()->updateOrCreate(
            ['key' => AppSetting::INTRO_ANIMATION_ENABLED],
            ['value' => $request->boolean(AppSetting::INTRO_ANIMATION_ENABLED) ? '1' : '0']
        );

        foreach ([
            AppSetting::THEME_DEFAULT => $data[AppSetting::THEME_DEFAULT],
            AppSetting::LOCALE_DEFAULT => $data[AppSetting::LOCALE_DEFAULT],
            AppSetting::ALLOW_THEME_SWITCH => $request->boolean(AppSetting::ALLOW_THEME_SWITCH) ? '1' : '0',
            AppSetting::ALLOW_LANGUAGE_SWITCH => $request->boolean(AppSetting::ALLOW_LANGUAGE_SWITCH) ? '1' : '0',
        ] as $key => $value) {
            AppSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('status', 'App settings updated.');
    }
}
