<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ClassStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case CANCELLED = 'cancelled';

    const GET = [
        self::ACTIVE,
        self::INACTIVE,
        self::CANCELLED
    ];

    const CLASSES = [
        self::ACTIVE->value => 'Active',
        self::INACTIVE->value => 'Inactive',
        self::CANCELLED->value => 'Cancelled'
    ];

    const GET_VALUES = [
        self::ACTIVE->value,
        self::INACTIVE->value,
        self::CANCELLED->value
    ];

    public function is($status): bool
    {
        return $this === $status;
    }

    public function isNot($status): bool
    {
        return !$this->is($status);
    }

    public static function get(): array
    {
        return self::GET;
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
