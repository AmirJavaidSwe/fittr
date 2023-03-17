<?php

namespace App\Enums;

enum SettingGroup
{
    case general_details;
    case general_address;
    case general_formats;
    case integrations;
    case service_store_general;
    case service_store_header;
    case service_store_seo;
    case service_store_code;
    case service_store_waivers;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }
}
