<?php

namespace App\Enums;

enum SettingGroup
{
    case general_details;
    case general_address;
    case general_formats;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }
}
