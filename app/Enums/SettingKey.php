<?php

namespace App\Enums;

use App\Enums\SettingGroup;
use App\Enums\CastType;

enum SettingKey: string
{
    //Business Settings
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
    
    //Online Store
    case subdomain = 'subdomain';
    case custom_domain = 'custom_domain';
    case logo = 'logo';
    case logo_url = 'logo_url';
    case favicon = 'favicon';
    case show_address = 'show_address';
    case show_phone = 'show_phone';
    case show_email = 'show_email';
    case link_facebook = 'link_facebook';
    case link_instagram = 'link_instagram';
    case link_twitter = 'link_twitter';
    case link_youtube = 'link_youtube';
    case meta_title = 'meta_title';
    case meta_description = 'meta_description';

    public const GROUP_GENERAL_DETAILS = SettingGroup::general_details->name;
    public const GROUP_GENERAL_ADDRESS = SettingGroup::general_address->name;
    public const GROUP_GENERAL_FORMATS = SettingGroup::general_formats->name;

    public const GROUP_SERVICE_STORE_GENERAL = SettingGroup::service_store_general->name;
    public const GROUP_SERVICE_STORE_HEADER = SettingGroup::service_store_header->name;
    public const GROUP_SERVICE_STORE_SEO = SettingGroup::service_store_seo->name;

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

            static::subdomain => ['required', 'string', 'max:255'],
            static::custom_domain => ['max:255'],

            static::logo => ['nullable', 'mimes:jpg,jpeg,png,svg', 'max:2048'], //2MB
            static::logo_url => ['required', 'max:5'],
            static::favicon => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'], //1MB
            static::show_address => ['required'],
            static::show_phone => ['required'],
            static::show_email => ['required'],
            static::link_facebook => ['nullable', 'string', 'max:255', 'regex:"^https:\/\/www.facebook.com\/"'],
            static::link_instagram => ['nullable', 'string', 'max:255', 'regex:"^https:\/\/www.instagram.com\/"'],
            static::link_twitter => ['nullable', 'string', 'max:255', 'regex:"^https:\/\/twitter.com\/"'],
            static::link_youtube => ['nullable', 'string', 'max:255', 'regex:"^https:\/\/www.youtube.com\/"'],

            static::meta_title => ['nullable', 'string', 'max:255'],
            static::meta_description => ['nullable', 'string', 'max:255'],

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

            static::subdomain,
            static::custom_domain => self::GROUP_SERVICE_STORE_GENERAL,

            static::logo,
            static::logo_url,
            static::favicon,
            static::show_address,
            static::show_phone,
            static::show_email,
            static::link_facebook,
            static::link_instagram,
            static::link_twitter,
            static::link_youtube => self::GROUP_SERVICE_STORE_HEADER,

            static::meta_title,
            static::meta_description => self::GROUP_SERVICE_STORE_SEO,

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
            static::zip_code,
            static::subdomain,
            static::custom_domain,
            static::logo,
            static::logo_url,
            static::favicon,
            static::link_facebook,
            static::link_instagram,
            static::link_twitter,
            static::link_youtube,
            static::meta_title,
            static::meta_description => CastType::string->name,

            static::country_id,
            static::legal_country_id,
            static::business_phone,
            static::date_format,
            static::time_format => CastType::integer->name,

            static::show_timezone,
            static::show_address,
            static::show_phone,
            static::show_email => CastType::boolean->name,
        };
    }
}
