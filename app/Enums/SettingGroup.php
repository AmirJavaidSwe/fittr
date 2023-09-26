<?php

namespace App\Enums;

enum SettingGroup
{
    case bookings;
    case general_details;
    case general_address;
    case general_formats;
    case integrations;
    case fap;
    case service_store_general;
    case service_store_header;
    case service_store_seo;
    case service_store_code;
    case service_store_waivers;

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
        // dd($case);
        return match(true) {
            $case == 'bookings' => static::bookings,
            $case == 'general_details' => static::general_details,
            $case == 'general_address' => static::general_address,
            $case == 'general_formats' => static::general_formats,
            $case == 'integrations' => static::integrations,
            $case == 'fap' => static::fap,
            $case == 'service_store_general' => static::service_store_general,
            $case == 'service_store_header' => static::service_store_header,
            $case == 'service_store_seo' => static::service_store_seo,
            $case == 'service_store_code' => static::service_store_code,
            $case == 'service_store_waivers' => static::service_store_waivers,
        };
    }
}
