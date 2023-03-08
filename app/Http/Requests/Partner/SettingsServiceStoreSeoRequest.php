<?php

namespace App\Http\Requests\Partner;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class SettingsServiceStoreSeoRequest extends FormRequest
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
            'meta_title' => SettingKey::meta_title->rules(),
            'meta_description' => SettingKey::meta_description->rules(),
        ];

        return $rules;
    }
}
