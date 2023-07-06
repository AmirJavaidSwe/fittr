<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum BookingStatus: int
{
    case ACTIVE = 1;
    case CANCELLED = 2;

    public static function all(): array
    {
        return array_column(self::cases(), 'name', 'value');
    }

    public static function labels(): array
    {
        return array(
            [
                'label' => Str::ucfirst(__(self::ACTIVE->name)),
                'value' => self::ACTIVE->value,
            ],
            [
                'label' => Str::ucfirst(__(self::CANCELLED->name)),
                'value' => self::CANCELLED->value,
            ]
        );
    }
}
