<?php

namespace App\Enums;

enum ClasspackType
{
    case class_lesson;
    case service;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function from(string $case)
    {
        return match(true) {
            $case == 'class_lesson' => static::class_lesson,
            $case == 'service' => static::service,
        };
    }

    public static function labels(): array
    {
        return array(
            [
                'label' => __('Class'),
                'value' => self::class_lesson->name,
            ],
            [
                'label' => __('Service'),
                'value' => self::service->name,
            ],
        );
    }
}
