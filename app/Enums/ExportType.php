<?php

namespace App\Enums;

use App\Models\Partner\Export;
use App\Exports\ExportClassLesson;
use App\Models\Partner\ClassLesson;
use App\Models\Partner\Instructor;
use App\Models\Partner\Studio;

enum ExportType
{
    case classes;
    case instructors;
    case studios;
//    case members;
//    case orders;

    case fileCsv  ;
    case fileXlsx;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function fileType(string $fileType): ExportType
    {
        return match($fileType) {
            'csv' => static::fileCsv,
            'xlsx' => static::fileXlsx,
        };
    }

    public static function from(string $case)
    {
        return match(true) {
            $case == 'classes' => static::classes,
        };
    }

    public static function operator(string $param)
    {
        return match($param) {
            'start_date' => ['whereDate', '>=', 'datetime'],
            'end_date' => ['whereDate', '<=', 'datetime'],
            default => ['where', '=', $param],
        };
    }

    public function model(): Studio|Instructor|ClassLesson
    {
        return match($this) {
            static::classes => new ClassLesson,
            static::instructors => new Instructor,
            static::studios => new Studio
        };
    }

    public function get(Export $export)
    {
        return match($this) {
            static::classes => (new ExportClassLesson($export))(),
        };
    }
}
