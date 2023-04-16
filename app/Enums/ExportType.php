<?php

namespace App\Enums;

use App\Models\Partner\ClassLesson;

enum ExportType
{
    case classes;
    // case members;
    // case instructors;
    // case studios;
    // case orders;

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
            default => ['where', '=', null],
        };
    }

    public function model()
    {
        return match($this) {
            static::classes => new ClassLesson,
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
