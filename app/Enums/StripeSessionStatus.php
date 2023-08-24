<?php

namespace App\Enums;

// https://stripe.com/docs/api/checkout/sessions/object

enum StripeSessionStatus
{
    case open; //The checkout session is still in progress. Payment processing has not started
    case complete; //The checkout session is complete. Payment processing may still be in progress
    case expired; //The checkout session has expired. No further processing will occur

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
            $case == 'open' => static::open,
            $case == 'complete' => static::complete,
            $case == 'expired' => static::expired,
        };
    }
}
