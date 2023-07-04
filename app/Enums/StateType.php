<?php

namespace App\Enums;

enum StateType: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function get(string $case): string
    {
        return self::from($case)->value;
    }
}
