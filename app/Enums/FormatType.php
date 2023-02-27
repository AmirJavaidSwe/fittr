<?php

namespace App\Enums;

enum FormatType
{
    case date;
    case time;
    case number;
    case money;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }
}
