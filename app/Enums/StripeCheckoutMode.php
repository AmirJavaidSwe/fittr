<?php

namespace App\Enums;

enum StripeCheckoutMode
{
    case payment;
    case subscription;
    case setup;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function get(string $case): string
    {
        return self::from($case)->name;
    }

    public static function from(string $case)
    {
        return match(true) {
            $case == 'payment' => static::payment,
            $case == 'subscription' => static::subscription,
            $case == 'setup' => static::setup,
        };
    }
}
