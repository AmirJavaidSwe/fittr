<?php

namespace App\Enums;

enum PackType
{
    case class_lesson;
    case service;
    case hybrid;
    case default;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function from(string $case)
    {
        return match(true) {
            $case == 'class_lesson' => static::class_lesson,
            $case == 'service' => static::service,
            $case == 'hybrid' => static::hybrid,
            $case == 'default' => static::default,
        };
    }

    public static function labels(): array
    {
        return array(
            [
                'label' => __('Class'),
                'value' => self::class_lesson->name,
                'description' => __('Plan provides fixed or unlimited number of sessions to classes.'),
            ],
            [
                'label' => __('Service'),
                'value' => self::service->name,
                'description' => __('Plan provides fixed or unlimited number of sessions for services.'),
            ],
            [
                'label' => __('Hybrid'),
                'value' => self::hybrid->name,
                'description' => __('Plan provides fixed or unlimited number of sessions to both, classes and services.'),
            ],
            [
                'label' => __('Default'),
                'value' => self::default->name,
                'description' => __('Plan provides general access or pass to studio only. This type will not provide any classes or services bookings.'),
            ],
        );
    }
}
