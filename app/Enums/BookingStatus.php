<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum BookingStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case CANCELLED = 'cancelled';

    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function get(string $case): string
    {
        return self::from($case)->value;
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
