<?php

namespace App\Enums;

enum CastType
{
    case string;
    case integer;
    case float;
    case boolean;
    case array;
    case json;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }
}
