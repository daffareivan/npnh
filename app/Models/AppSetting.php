<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AppSetting extends Model
{
    public const INTRO_ANIMATION_ENABLED = 'intro_animation_enabled';
    public const THEME_DEFAULT = 'theme_default';
    public const LOCALE_DEFAULT = 'locale_default';
    public const ALLOW_THEME_SWITCH = 'allow_theme_switch';
    public const ALLOW_LANGUAGE_SWITCH = 'allow_language_switch';

    protected $fillable = ['key', 'value'];

    public static function valueFor(string $key, mixed $default = null): mixed
    {
        if (! Schema::hasTable('app_settings')) {
            return $default;
        }

        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function boolean(string $key, bool $default = false): bool
    {
        return filter_var(static::valueFor($key, $default ? '1' : '0'), FILTER_VALIDATE_BOOLEAN);
    }
}
