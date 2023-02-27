<?php

namespace App\Enums;

use App\Enums\SettingGroup;
use App\Enums\CastType;

enum SettingKey: string
{
    case business_name = 'business_name';
    case business_email = 'business_email';
    case country_id = 'country_id';
    case business_phone = 'business_phone';
    case timezone = 'timezone';
    case show_timezone = 'show_timezone';
    case address_line1 = 'address_line1';
    case address_line2 = 'address_line2';
    case city = 'city';
    case state = 'state';
    case legal_country_id = 'legal_country_id';
    case zip_code = 'zip_code';
    case date_format = 'date_format';
    case time_format = 'time_format';

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
            static::business_phone => ['required','min:4', 'max:12', 'regex:"^[0-9]+$"'],
            static::timezone => ['required','exists:timezones,title'],
            static::show_timezone => ['required','boolean'],
            static::address_line1 => ['required', 'string', 'max:256'],
            static::address_line2 => ['nullable', 'string', 'max:256'],
            static::city => ['required', 'string', 'max:256'],
            static::state => ['max:256'],
            static::zip_code => ['required', 'string', 'max:256'],
            static::legal_country_id => ['required','exists:countries,id'],
            static::date_format => ['required', 'exists:formats,id'],
            static::time_format => ['required', 'exists:formats,id'],
            default => [],
        };
    }

    public function group(): string
    {
        return match($this) {
            static::business_name,
            static::business_email,
            static::country_id,
            static::business_phone,
            static::show_timezone,
            static::timezone => self::GROUP_GENERAL_DETAILS,

            static::address_line1,
            static::address_line2,
            static::city,
            static::state,
            static::legal_country_id,
            static::zip_code => self::GROUP_GENERAL_ADDRESS,

            static::date_format,
            static::time_format => self::GROUP_GENERAL_FORMATS,
        };
    }

    public function cast(): string
    {
        return match($this) {
            static::business_name,
            static::business_email,
            static::timezone,
            static::address_line1,
            static::address_line2,
            static::city,
            static::state,
            static::zip_code => CastType::string->name,

            static::country_id,
            static::legal_country_id,
            static::business_phone,
            static::date_format,
            static::time_format => CastType::integer->name,

            static::show_timezone => CastType::boolean->name,
        };
    }
}
