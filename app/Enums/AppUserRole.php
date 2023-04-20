<?php

namespace App\Enums;

enum AppUserRole: string
{
    //TODO: add staff role - to be assigned to staff users, created by partner. Permissions to be added by partner
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
