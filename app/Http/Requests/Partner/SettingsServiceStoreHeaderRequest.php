<?php

namespace App\Http\Requests\Partner;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class SettingsServiceStoreHeaderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->is_partner;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        
        $rules = [
            'logo' => SettingKey::logo->rules(),
            'logo_url' => SettingKey::logo_url->rules(),
            'favicon' => SettingKey::favicon->rules(),
            'show_address' => SettingKey::show_address->rules(),
            'show_phone' => SettingKey::show_phone->rules(),
            'show_email' => SettingKey::show_email->rules(),
            'link_facebook' => SettingKey::link_facebook->rules(),
            'link_instagram' => SettingKey::link_instagram->rules(),
            'link_twitter' => SettingKey::link_twitter->rules(),
            'link_youtube' => SettingKey::link_youtube->rules(),
        ];

        return $rules;
    }
}
