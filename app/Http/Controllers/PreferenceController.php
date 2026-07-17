<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class PreferenceController extends Controller
{
    public function theme(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', 'in:light,dark,system'],
        ]);

        if ($request->user()) {
            $request->user()->forceFill(['theme' => $data['theme']])->save();
        }

        $request->session()->put('theme', $data['theme']);

        return back()->withCookie(Cookie::create('npnhcreative_theme', $data['theme'], 60 * 24 * 365));
    }

    public function locale(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'in:id,en'],
        ]);

        if ($request->user()) {
            $request->user()->forceFill(['locale' => $data['locale']])->save();
        }

        $request->session()->put('locale', $data['locale']);

        return back()->withCookie(Cookie::create('npnhcreative_locale', $data['locale'], 60 * 24 * 365));
    }
}
