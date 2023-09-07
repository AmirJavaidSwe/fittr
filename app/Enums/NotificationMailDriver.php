<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum NotificationMailDriver: string
{
    case SMTP = 'smtp';
    case SENDGRID = 'sendgrid';
    case AWS_SES = 'aws_ses';

    public static function all(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function get(string $case): string
    {
        return self::from($case)->value;
    }
}
