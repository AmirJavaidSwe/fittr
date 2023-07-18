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
        ];
    }
}
