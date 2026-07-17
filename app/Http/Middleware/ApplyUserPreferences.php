<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ApplyUserPreferences
{
    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = AppSetting::valueFor(AppSetting::LOCALE_DEFAULT, config('app.locale', 'en'));
        $locale = $request->user()?->locale
            ?: $request->session()->get('locale')
            ?: $request->cookie('npnhcreative_locale')
            ?: $defaultLocale;

        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = 'en';
        }

        App::setLocale($locale);

        $theme = $request->user()?->theme
            ?: $request->session()->get('theme')
            ?: $request->cookie('npnhcreative_theme')
            ?: AppSetting::valueFor(AppSetting::THEME_DEFAULT, 'system');

        if (! in_array($theme, ['light', 'dark', 'system'], true)) {
            $theme = 'system';
        }

        View::share('currentLocale', $locale);
        View::share('currentTheme', $theme);
        View::share('allowThemeSwitch', AppSetting::boolean(AppSetting::ALLOW_THEME_SWITCH, true));
        View::share('allowLanguageSwitch', AppSetting::boolean(AppSetting::ALLOW_LANGUAGE_SWITCH, true));

        return $next($request);
    }
}
