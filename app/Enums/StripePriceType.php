<?php

namespace App\Enums;

enum StripePriceType
{
    case one_time;
    case recurring;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function labels(): array
    {
        return array(
            [
                'label' => __('One time'),
                'value' => self::one_time->name,
                'description' => __('Single purchase of membership.'),
            ],
            [
                'label' => __('Recurring'),
                'value' => self::recurring->name,
                'description' => __('Subscription billing with charges on specified interval.'),
            ],
        );
    }
}
