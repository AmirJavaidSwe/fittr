<?php

namespace App\Enums;

enum PackType
{
    case class_lesson;
    case service;
    case hybrid;
    case default;
    case corporate;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function creditTypes(): array
    {
        return array_column(array(static::class_lesson, static::service, static::hybrid), 'name');
    }

    public static function from(string $case)
    {
        return match(true) {
            $case == 'class_lesson' => static::class_lesson,
            $case == 'service' => static::service,
            $case == 'hybrid' => static::hybrid,
            $case == 'default' => static::default,
            $case == 'corporate' => static::corporate,
        };
    }

    public static function get(string $case): string
    {
        return self::from($case)->name;
    }

    public static function labels(): array
    {
        return array(
            [
                'label' => __('Class'),
                'label_plural' => __('Classes'),
                'value' => self::class_lesson->name,
                'description' => __('Plan provides fixed or unlimited number of sessions that can be used to book classes.'),
                'order' => 2,
            ],
            [
                'label' => __('Service'),
                'label_plural' => __('Services'),
                'value' => self::service->name,
                'description' => __('Plan provides fixed or unlimited number of sessions that can be used to book services.'),
                'order' => 3,
            ],
            [
                'label' => __('Hybrid'),
                'label_plural' => __('Hybrids'),
                'value' => self::hybrid->name,
                'description' => __('Plan provides fixed or unlimited number of sessions to both, classes and services.'),
                'order' => 4,
            ],
            [
                'label' => __('Pass'),
                'label_plural' => __('Passes'),
                'value' => self::default->name,
                'description' => __('Plan provides general access or pass to studio only. This type will not generate any session credits.'),
                'order' => 1,
            ],
            [
                'label' => __('Corporate'),
                'label_plural' => __('Corporate'),
                'value' => self::corporate->name,
                'description' => __('Corporate plans will produce unique redemption code. Everytime member redeems it, one session credit will be added. Member can use the credit to book a class.'),
                'order' => 5,
            ],
        );
    }

    // Existing pack type can only be one of 'class_lesson, service, hybrid' or 'default' or 'corporate': ('default' and 'corporate' can't be chaged to any other type)
    public static function labelsForExisting($for = null): array
    {
        $grouped = [self::class_lesson->name, self::service->name, self::hybrid->name];

        $filtered = array_filter(self::labels(), function($item) use ($for, $grouped) {
            switch ($for) {
                case self::default->name:
                    return $for === $item['value'];
                case self::class_lesson->name:
                    return in_array($item['value'], $grouped);
                case self::service->name:
                    return in_array($item['value'], $grouped);
                case self::hybrid->name:
                    return in_array($item['value'], $grouped);
                case self::corporate->name:
                    return $for === $item['value'];
                default:
                    return false;
            }
        });
        return array_values($filtered);
    }

    public static function creditable($value): bool
    {
        return match($value) {
            'class_lesson', 'service', 'hybrid' => true,
            default => false,
        };
    }
}
