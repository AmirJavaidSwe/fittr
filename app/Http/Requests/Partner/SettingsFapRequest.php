<?php

namespace App\Http\Requests\Partner;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class SettingsFapRequest extends FormRequest
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
        return[
            'fap_status' => SettingKey::fap_status->rules(),
            'fap_max' => SettingKey::fap_max->rules(),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fap_max.required' => __('Number of bookings is required.'),
            'fap_max.min' => __('Number of bookings must be at least :min.'),
            'fap_max.max' => __('Number of bookings must not be greater than :max.'),
        ];
    }
}
