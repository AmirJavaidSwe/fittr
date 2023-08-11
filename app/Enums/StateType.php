<?php

namespace App\Enums;

use Illuminate\Support\Str;

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

    public static function labels(): array
    {
        return array(
            [
                'label' => Str::ucfirst(__(self::ACTIVE->value)),
                'value' => self::ACTIVE->value,
                'color' => '#339933',
            ],
            [
                'label' => Str::ucfirst(__(self::INACTIVE->value)),
                'value' => self::INACTIVE->value,
                'color' => '#939393',
            ],
        );
    }
}
