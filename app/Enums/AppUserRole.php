<?php

namespace App\Enums;

enum AppUserRole: string
{
    case PARTNER = 'partner';
    case ADMIN = 'admin';

    public static function roles(): array
    {
        return array(
            self::PARTNER->name => self::PARTNER->value,
            self::ADMIN->name => self::ADMIN->value,
        );
    }
}
