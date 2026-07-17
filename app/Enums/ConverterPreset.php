<?php

declare(strict_types=1);

namespace App\Enums;

enum ConverterPreset: string
{
    case Speed23 = '2.3';
    case Speed25 = '2.5';
    case Speed27 = '2.7';

    public function amplifyDb(): int
    {
        return match ($this) {
            self::Speed23 => -4,
            self::Speed25 => -6,
            self::Speed27 => -8,
        };
    }

    public static function default(): self
    {
        return self::Speed23;
    }
}
