<?php

namespace App\Http\Requests\Partner;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class SettingsIntegrationsRequest extends FormRequest
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
            'integration_mailchimp_status' => SettingKey::integration_mailchimp_status->rules(),
            'integration_mailchimp_api_key' => SettingKey::integration_mailchimp_api_key->rules(),
            'integration_mailchimp_list_id' => SettingKey::integration_mailchimp_list_id->rules(),
            'integration_sendgrid_status' => SettingKey::integration_sendgrid_status->rules(),
            'integration_sendgrid_api_key' => SettingKey::integration_sendgrid_api_key->rules(),
            'integration_sendgrid_list_id' => SettingKey::integration_sendgrid_list_id->rules(),
            'integration_sendinblue_status' => SettingKey::integration_sendinblue_status->rules(),
            'integration_sendinblue_api_key' => SettingKey::integration_sendinblue_api_key->rules(),
            'integration_sendinblue_list_id' => SettingKey::integration_sendinblue_list_id->rules(),
        ];

        return $rules;
    }
}
