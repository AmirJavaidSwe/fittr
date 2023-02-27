<?php

namespace App\Http\Requests\Partner;

use App\Enums\SettingKey;
use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;

class SettingsGeneralAddressRequest extends FormRequest
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
            'address_line1' => SettingKey::address_line1->rules(),
            'address_line2' => SettingKey::address_line2->rules(),
            'city' => SettingKey::city->rules(),
            'state' => SettingKey::state->rules(),
            'legal_country_id' => SettingKey::legal_country_id->rules(),
            'zip_code' => SettingKey::zip_code->rules(),
        ];

        //add ad-hoc rule for state when country has any
        if($this->countryWithStates()){
            $rules['state'][] = 'required';
        }

        return $rules;
    }

    public function countryWithStates() :bool
    {
        if(!empty($this->legal_country_id)){
            $country = Country::find($this->legal_country_id);
        }
        return !empty($country) && $country->has_states;
    }
}
