<?php

namespace App\Enums;

use App\Enums\SettingGroup;
use App\Enums\CastType;

enum SettingKey: string
{
    case business_name = 'business_name';
    case business_email = 'business_email';
    case country_id = 'country_id';

    public const GROUP_GENERAL_DETAILS = SettingGroup::general_details->name;
    public const GROUP_GENERAL_ADDRESS = SettingGroup::general_address->name;
    public const GROUP_GENERAL_FORMATS = SettingGroup::general_formats->name;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public function rules(): array
    {
        return match($this) {
            static::business_name => ['required','string'],
            static::business_email => ['required','email'],
            static::country_id => ['required','exists:countries,id'],
            default => [],
        };
    }

    public function group(): string
    {
        return match($this) {
            static::business_name,
            static::business_email,
            static::country_id => self::GROUP_GENERAL_DETAILS,
        };
    }

    public function cast(): string
    {
        return match($this) {
            static::business_name,
            static::business_email => CastType::string->name,
            static::country_id => CastType::integer->name,
        };
    }
}
