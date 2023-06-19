<?php

namespace App\Http\Requests\Partner;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class BusinessOnboardRequest extends FormRequest
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
            'business_name' => SettingKey::business_name->rules(),
            'business_email' => SettingKey::business_email->rules(),
            'country_id' => SettingKey::country_id->rules(),
            'business_phone' => SettingKey::business_phone->rules(),
            'timezone' => SettingKey::timezone->rules(),
            'subdomain' => SettingKey::subdomain->rules(),
            'date_format' => SettingKey::date_format->rules(),
            'time_format' => SettingKey::time_format->rules(),
        ];

        return $rules;
    }
}
