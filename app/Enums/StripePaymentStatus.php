<?php

namespace App\Enums;

// https://stripe.com/docs/api/checkout/sessions/object

enum StripePaymentStatus
{
    case paid; //The payment funds are available in your account.
    case unpaid; //The payment funds are not yet available in your account.
    case no_payment_required; //The payment is delayed to a future date, or the Checkout Session is in setup mode and doesn’t require a payment at this time.

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
