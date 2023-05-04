<?php

namespace App\Http\Requests\Partner;

use App\Enums\ClasspackType;
use App\Enums\ClasspackExpirationPeriod;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ClasspackFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules =  [
            'title' => 'required|string|max:255',
            'sessions' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_expiring' => 'boolean',
            'is_restricted' => 'boolean',
            'restrictions' => 'exclude_if:is_restricted,false|array:offpeak,classtypes',
            'expiration' => 'required_if:is_expiring,true|nullable|integer',
            'expiration_period' => ['required_if:is_expiring,true', 'nullable', Rule::in(ClasspackExpirationPeriod::all())],
            'price' => 'required|numeric',
            'type' => ['required', Rule::in(ClasspackType::all())],
            'is_renewable' => 'boolean|declined_if:is_intro,true',
            'is_intro' => 'boolean|declined_if:is_renewable,true',
            'is_private' => 'boolean',
            'private_url' => 'required_if:is_private,true|nullable|regex:"^[a-z0-9-]+$"|unique:mysql_partner.classpacks,private_url,'.$this->classpack?->id,
            'active_from' => 'nullable|date|exclude_if:active_to,null|before:active_to',
            'active_to' => 'nullable|date|after:active_from',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'expiration.required_if' => __('Please set expiration value.'),
            'expiration_period.required_if' => __('Please select expiration period.'),
            'private_url.required_if' => __('Private packs must have an URL.'),
            'private_url.regex' => __('URL should have lowercase letters, numbers and hyphens only.'),
            'is_renewable.declined_if' => __('One of the fields must be disabled.'),
            'is_intro.declined_if' => __('One of the fields must be disabled.'),
        ];
    }
}
