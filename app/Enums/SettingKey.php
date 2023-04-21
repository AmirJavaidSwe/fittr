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
    case integration_mailchimp_status = 'integration_mailchimp_status';
    case integration_mailchimp_api_key = 'integration_mailchimp_api_key';
    case integration_mailchimp_list_id = 'integration_mailchimp_list_id';
    case integration_sendgrid_status = 'integration_sendgrid_status';
    case integration_sendgrid_api_key = 'integration_sendgrid_api_key';
    case integration_sendgrid_list_id = 'integration_sendgrid_list_id';
    case integration_sendinblue_status = 'integration_sendinblue_status';
    case integration_sendinblue_api_key = 'integration_sendinblue_api_key';
    case integration_sendinblue_list_id = 'integration_sendinblue_list_id';
    
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
    case google_analytics = 'google_analytics';
    case google_gtag = 'google_gtag';
    case google_adsense = 'google_adsense';
    case fb_pixel = 'fb_pixel';
    
    //Waivers
    case waiver_text = 'waiver_text';
    case enforce_waiver = 'enforce_waiver';

    public const GROUP_GENERAL_DETAILS = SettingGroup::general_details->name;
    public const GROUP_GENERAL_ADDRESS = SettingGroup::general_address->name;
    public const GROUP_GENERAL_FORMATS = SettingGroup::general_formats->name;

    public const GROUP_INTEGRATIONS = SettingGroup::integrations->name;

    public const GROUP_SERVICE_STORE_GENERAL = SettingGroup::service_store_general->name;
    public const GROUP_SERVICE_STORE_HEADER = SettingGroup::service_store_header->name;
    public const GROUP_SERVICE_STORE_SEO = SettingGroup::service_store_seo->name;
    public const GROUP_SERVICE_STORE_CODE = SettingGroup::service_store_code->name;
    public const GROUP_SERVICE_STORE_WAIVERS = SettingGroup::service_store_waivers->name;

    public static function all(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function files(): array
    {
        return array_column([
            static::logo,
            static::favicon,
        ], 'name');
    }

    public function rules(): array
    {
        return match($this) {
            //Business Settings
            // Business Details
            static::business_name => ['required','string'],
            static::business_email => ['required','email'],
            static::country_id => ['required','exists:countries,id'],
            static::business_phone => ['required','min:4', 'max:12', 'regex:"^[0-9]+$"'],
            static::timezone => ['required','exists:timezones,title'],
            static::show_timezone => ['boolean'],

            // Legal Address
            static::address_line1 => ['required', 'string', 'max:256'],
            static::address_line2 => ['nullable', 'string', 'max:256'],
            static::city => ['required', 'string', 'max:256'],
            static::state => ['max:256'],
            static::zip_code => ['required', 'string', 'max:256'],
            static::legal_country_id => ['required','exists:countries,id'],

            // Formats
            static::date_format => ['required', 'exists:formats,id'],
            static::time_format => ['required', 'exists:formats,id'],

            // Integrations
            static::integration_mailchimp_status => ['boolean'],
            static::integration_mailchimp_api_key => ['nullable', 'string', 'max:255'],
            static::integration_mailchimp_list_id => ['nullable', 'string', 'max:255'],
            static::integration_sendgrid_status => ['boolean'],
            static::integration_sendgrid_api_key => ['nullable', 'string', 'max:255'],
            static::integration_sendgrid_list_id => ['nullable', 'string', 'max:255'],
            static::integration_sendinblue_status => ['boolean'],
            static::integration_sendinblue_api_key => ['nullable', 'string', 'max:255'],
            static::integration_sendinblue_list_id => ['nullable', 'string', 'max:255'],

            //Online Store
            // General
            static::subdomain => ['required', 'max:255', 'regex:"^[a-z0-9-]+$"'],
            static::custom_domain => ['nullable', 'max:255', 'regex:"^[a-z0-9-.]+$"'],

            // Header & Footer
            static::logo => ['nullable', 'mimes:jpg,jpeg,png,svg', 'max:2048'], //2MB
            static::logo_url => ['nullable', 'max:255', 'regex:"^(http:\/\/|https:\/\/)"'],
            static::favicon => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'], //1MB
            static::show_address => ['nullable', 'boolean'],
            static::show_phone => ['nullable', 'boolean'],
            static::show_email => ['nullable', 'boolean'],
            static::link_facebook => ['nullable', 'string', 'max:255', 'regex:"^https:\/\/www.facebook.com\/"'],
            static::link_instagram => ['nullable', 'string', 'max:255', 'regex:"^https:\/\/www.instagram.com\/"'],
            static::link_twitter => ['nullable', 'string', 'max:255', 'regex:"^https:\/\/twitter.com\/"'],
            static::link_youtube => ['nullable', 'string', 'max:255', 'regex:"^https:\/\/www.youtube.com\/"'],

            // SEO
            static::meta_title => ['nullable', 'string', 'max:255'],
            static::meta_description => ['nullable', 'string', 'max:255'],

            // Custom code
            static::google_analytics => ['nullable', 'string', 'max:255', 'regex:"^UA-"'],
            static::google_gtag => ['nullable', 'string', 'max:255', 'regex:"^GTM-"'],
            static::google_adsense => ['nullable', 'string', 'max:255', 'regex:"^ca-pub-"'],
            static::fb_pixel => ['nullable', 'digits:15'],

            // Waivers
            static::waiver_text => ['nullable', 'string', 'max:65535'],
            static::enforce_waiver => ['boolean'],

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

            static::integration_mailchimp_status,
            static::integration_mailchimp_api_key,
            static::integration_mailchimp_list_id,
            static::integration_sendgrid_status,
            static::integration_sendgrid_api_key,
            static::integration_sendgrid_list_id,
            static::integration_sendinblue_status,
            static::integration_sendinblue_api_key,
            static::integration_sendinblue_list_id => self::GROUP_INTEGRATIONS,

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

            static::google_analytics,
            static::google_gtag,
            static::google_adsense,
            static::fb_pixel => self::GROUP_SERVICE_STORE_CODE,

            static::waiver_text,
            static::enforce_waiver => self::GROUP_SERVICE_STORE_WAIVERS,

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
            static::integration_mailchimp_api_key,
            static::integration_mailchimp_list_id,
            static::integration_sendgrid_api_key,
            static::integration_sendgrid_list_id,
            static::integration_sendinblue_api_key,
            static::integration_sendinblue_list_id,
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
            static::meta_description,
            static::google_analytics,
            static::google_gtag,
            static::google_adsense,
            static::fb_pixel,
            static::waiver_text => CastType::string->name,

            static::country_id,
            static::legal_country_id,
            static::business_phone,
            static::date_format,
            static::time_format => CastType::integer->name,

            static::show_timezone,
            static::show_address,
            static::show_phone,
            static::show_email,
            static::integration_mailchimp_status,
            static::integration_sendgrid_status,
            static::integration_sendinblue_status,
            static::enforce_waiver => CastType::boolean->name,
        };
    }

    //keys that shoud be encrypted on save and decrypted on read
    public function encryption(): bool
    {
        return match($this) {
            static::integration_mailchimp_api_key,
            static::integration_sendgrid_api_key,
            static::integration_sendinblue_api_key  => true,

            default => false,
        };
    }
}
