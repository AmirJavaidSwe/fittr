<?php

namespace App\Enums;

enum Subscription: string
{
    case MONTH = 'monthly';
    case YEAR = 'annually';

    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case TRIALING = 'trialing';

    public static function periods(): array
    {
        return array(
            self::MONTH->name => self::MONTH->value,
            self::YEAR->name => self::YEAR->value,
        );
    }

    public static function statuses(): array
    {
        return array(
            self::ACTIVE->name => self::ACTIVE->value,
            self::CANCELLED->name => self::CANCELLED->value,
            self::TRIALING->name => self::TRIALING->value,
        );
    }
}
