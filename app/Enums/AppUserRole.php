<?php

namespace App\Enums;

enum AppUserRole: string
{
    //TODO: Stay with 2 roles only. We need to add permissions/abilities to both roles
    //There may be fittr admins with full or limited abilities and same for partner
    case PARTNER = 'partner';
    case ADMIN = 'admin';

    public static function roles(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }
}
