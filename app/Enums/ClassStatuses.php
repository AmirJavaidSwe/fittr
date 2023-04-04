<?php

namespace App\Enums;

enum ClassStatuses: string
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

    const GET_VALUES_WITH_LABELS = [
        [
            'label' => self::ACTIVE->value,
            'value' => self::ACTIVE->value,
        ],
        [
            'label' => self::INACTIVE->value,
            'value' => self::INACTIVE->value,
        ],
        [
            'label' => self::CANCELLED->value,
            'value' => self::CANCELLED->value,
        ]
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
}
