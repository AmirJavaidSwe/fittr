<?php

namespace App\Enums;

enum PartnerUserRole: string
{
    case MEMBER = 'member';
    case INSTRUCTOR = 'instructor';

    public static function roles(): array
    {
        return array(
            self::MEMBER->name => self::MEMBER->value,
            self::INSTRUCTOR->name => self::INSTRUCTOR->value,
        );
    }
}
