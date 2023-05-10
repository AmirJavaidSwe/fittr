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
}
