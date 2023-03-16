<?php

namespace App\Http\Requests\Partner;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class SettingsServiceStoreCodeRequest extends FormRequest
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
            'google_analytics' => SettingKey::google_analytics->rules(),
            'google_gtag' => SettingKey::google_gtag->rules(),
            'google_adsense' => SettingKey::google_adsense->rules(),
            'fb_pixel' => SettingKey::fb_pixel->rules(),
        ];

        return $rules;
    }
}
