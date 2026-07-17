<?php

declare(strict_types=1);

namespace App\Http\Controllers\Converter;

use App\Http\Controllers\Controller;
use App\Models\AudioFile;
use App\Models\ConversionPreset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ConverterPageController extends Controller
{
    public function dashboard(): View
    {
        return $this->converter();
    }

    public function converter(): View
    {
        return view('pages.converter.dashboard', [
            'title' => 'NPNHCREATIVE',
            'presets' => ConversionPreset::query()->orderBy('speed')->get(),
            'defaultPreset' => ConversionPreset::query()->where('is_default', true)->first(),
        ]);
    }

    public function history(Request $request): View
    {
        return view('pages.converter.history', [
            'title' => 'NPNHCREATIVE History',
            'files' => AudioFile::query()
                ->with('preset')
                ->when($request->user(), fn ($query, $user) => $query->where('user_id', $user->id))
                ->latest()
                ->paginate(10),
        ]);
    }

    public function settings(): View
    {
        return view('pages.converter.settings', [
            'title' => 'NPNHCREATIVE Settings',
            'presets' => ConversionPreset::query()->orderBy('speed')->get(),
            'settings' => config('converter'),
        ]);
    }
}
