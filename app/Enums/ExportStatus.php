<?php

namespace App\Enums;

enum ExportStatus
{
    case pending;
    case processing;
    case completed;
    case failed;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }
}
