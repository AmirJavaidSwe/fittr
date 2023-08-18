<?php

namespace App\Enums;

enum StripePaymentStatus
{
    case paid;
    case unpaid;
    case no_payment_required;

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
            $case == 'paid' => static::paid,
            $case == 'unpaid' => static::unpaid,
            $case == 'no_payment_required' => static::no_payment_required,
        };
    }
}
