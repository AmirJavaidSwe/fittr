<?php

namespace App\Enums;

enum Instance: string
{
    case RUNNING = 'running';
    case STOPPED = 'stopped';
    case PENDING = 'pending';
    case INACTIVE = 'inactive';

    public static function statuses(): array
    {
        return array(
            self::RUNNING->name => self::RUNNING->value,
            self::STOPPED->name => self::STOPPED->value,
            self::PENDING->name => self::PENDING->value,
            self::INACTIVE->name => self::INACTIVE->value,
        );
    }
}
