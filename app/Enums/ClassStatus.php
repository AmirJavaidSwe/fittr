<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ClassStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case CANCELLED = 'cancelled';

    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function labels(): array
    {
        return array(
            [
                'label' => Str::ucfirst(__(self::ACTIVE->value)),
                'value' => self::ACTIVE->value,
            ],
            [
                'label' => Str::ucfirst(__(self::INACTIVE->value)),
                'value' => self::INACTIVE->value,
            ],
            [
                'label' => Str::ucfirst(__(self::CANCELLED->value)),
                'value' => self::CANCELLED->value,
            ]
        );
    }
}