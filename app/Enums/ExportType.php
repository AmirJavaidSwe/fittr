<?php

namespace App\Enums;

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

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
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

    public function model()
    {
        return match($this) {
            static::classes => new ClassLesson,
            static::instructors => new Instructor,
            static::studios => new Studio
        };
    }

    public function get(array $filters)
    {
        return match($this) {
            static::classes => (new ExportClassLesson($filters))(),
        };
    }

    // public function fields()
    // {
    //     return match($this) {
    //         static::classes => array(

    //         ),
    //     };
    // }
}
