<?php

namespace App\Enums;

// https://stripe.com/docs/api/invoices/object#invoice_object-status

enum StripeInvoiceStatus
{
    case draft; //The invoice isn’t ready to use. All invoices start in draft status.
    case open; //The invoice is finalized and awaiting payment.
    case paid; //This invoice is paid
    case uncollectible; //This invoice is canceled.
    case void; //The customer is unlikely to pay the invoice. Normally, you treat it as bad debt in your accounting process.

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
            $case == 'draft' => static::draft,
            $case == 'open' => static::open,
            $case == 'paid' => static::paid,
            $case == 'uncollectible' => static::uncollectible,
            $case == 'void' => static::void,
        };
    }
}
