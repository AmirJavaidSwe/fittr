<?php

namespace App\Enums;

enum StripePeriod
{
    case day;
    case week;
    case month;
    case year;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function labels(): array
    {
        return array(
            [
                'label' => __('Day'),
                'value' => self::day->name,
            ],
            [
                'label' => __('Week'),
                'value' => self::week->name,
            ],
            [
                'label' => __('Month'),
                'value' => self::month->name,
            ],
            [
                'label' => __('Year'),
                'value' => self::year->name,
            ],
        );
    }
}
