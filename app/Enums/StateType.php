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
}
