<?php

namespace App\Http\Requests\Partner;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class SettingsServiceStoreGeneralRequest extends FormRequest
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
            'subdomain' => SettingKey::subdomain->rules(),
            'custom_domain' => SettingKey::custom_domain->rules(),
        ];

        return $rules;
    }
}
