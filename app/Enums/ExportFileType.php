<?php

namespace App\Enums;

enum ExportFileType
{
    case csv;
    case xls;
    case xlsx;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }
    public static function getMimeType($type): string
    {
        return match ($type) {
            self::csv->name => 'text/csv',
            self::xls->name => 'application/vnd.ms-excel',
            self::xlsx->name => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => 'application/octet-stream'
        };
    }
}
