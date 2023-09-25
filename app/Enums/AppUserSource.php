<?php

namespace App\Enums;

enum AppUserSource
{
    case partner;
    case admin;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function from(string $case)
    {
        return match(true) {
            $case == 'admin' => static::admin,
            $case == 'partner' => static::partner,
        };
    }

    public static function get(string $case): string
    {
        return self::from($case)->name;
    }
}
