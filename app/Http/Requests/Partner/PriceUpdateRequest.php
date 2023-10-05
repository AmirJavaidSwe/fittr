<?php

namespace App\Http\Requests\Partner;

use App\Enums\PackType;
use App\Enums\StripePriceType;
use App\Enums\StripePeriod;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PriceUpdateRequest extends FormRequest
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
            'type' => ['required', Rule::in(StripePriceType::all())],
            'location_ids' => [
                'nullable',
                'exists:mysql_partner.locations,id'
            ],
            'sessions' => [
                'sometimes',
                Rule::excludeIf(
                    ($this->pack_type == PackType::get('location_pass') && $this->pass_mode === false) ||
                    //[class, service or hybrid] && recurring && unlimited
                    ($this->pack_type != PackType::get('location_pass') && $this->type == StripePriceType::get('recurring') && $this->is_unlimited == true)
                ),
                Rule::requiredIf(
                    ($this->pack_type == PackType::get('location_pass') && $this->pass_mode === true) ||
                    ($this->pack_type != PackType::get('location_pass') && $this->type == StripePriceType::get('recurring') && $this->is_unlimited == true)
                ),
                'integer',
                'min:1'
            ],
            'is_unlimited' => [
                'boolean',
                Rule::excludeIf($this->type == StripePriceType::get('one_time')),
            ],
            'pass_mode' => 'boolean',
            'is_expiring' => [
                'boolean',
                // Rule::excludeIf($this->pack_type == PackType::location_pass->name),
            ],
            'expiration' => 'required_if:is_expiring,true|exclude_without:is_expiring|exclude_if:is_expiring,false|integer|min:1',
            'expiration_period' => [
                'required_if:is_expiring,true',
                'exclude_without:is_expiring',
                'exclude_if:is_expiring,false',
                Rule::in(StripePeriod::all()),
            ],
            'is_renewable' => [
                'boolean',
                Rule::excludeIf($this->type != StripePriceType::get('one_time')),
                'declined_if:is_intro,true',
            ],
            'is_intro' => [
                'boolean',
                Rule::excludeIf($this->type != StripePriceType::get('one_time')),
                'declined_if:is_renewable,true',
            ],
            'is_ongoing' => 'boolean|exclude_if:type,'.StripePriceType::get('one_time'),
            'fixed_count' => 'required_if:is_ongoing,false|exclude_if:is_ongoing,true|exclude_without:is_ongoing|integer|min:1|gte:min_term',
            'min_term' => [
                'exclude_if:type,'.StripePriceType::get('one_time'),
                'nullable',
                'integer',
                'min:1'
            ],
            'is_fap' => 'boolean|required_if:is_unlimited,true',
            'fap_description' => 'required_if:is_fap,true|string|max:150',
            'fap_value' => [
                Rule::excludeIf($this->type == StripePriceType::get('one_time')),
                Rule::requiredIf($this->type == StripePriceType::get('recurring') && $this->is_fap),
                'integer',
                'min:1'
            ],
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
            'is_unlimited.declined_if' => __('Unlimited option is not available for default type pack.'),
            'fixed_count.required_if' => __('Please provide the number of billing cycles.'),
            'fixed_count.gte' => __('Number of subscription cycles must be greater than or equal min term duration.'),
        ];
    }
}
