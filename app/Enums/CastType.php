<?php

namespace App\Enums;

enum CastType
{
    case string;
    case integer;
    case float;
    case boolean;
    case array;
    case json;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function get(string $case): string
    {
        return self::from($case)->name;
    }

    public static function from(string $case)
    {
        return match(true) {
            $case == 'string' => static::string,
            $case == 'integer' => static::integer,
            $case == 'float' => static::float,
            $case == 'boolean' => static::boolean,
            $case == 'array' => static::array,
            $case == 'json' => static::json,
        };
    }
}
