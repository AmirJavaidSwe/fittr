<?php

namespace App\Http\Requests\Partner;

use App\Enums\PackType;
use App\Enums\StripePeriod;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PackFormRequest extends FormRequest
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
            'type' => ['required', Rule::in(PackType::all())],
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:65535',
            'is_active' => 'boolean',
            'is_restricted' => 'boolean',
            'is_private' => 'boolean',
            'restrictions' => 'exclude_if:is_restricted,false|array:offpeak,classtypes,servicetypes',
            'private_url' => 'required_if:is_private,true|nullable|regex:"^[a-z0-9-]+$"|unique:mysql_partner.packs,private_url,'.$this->pack?->id,
            'active_from' => 'nullable|date',
            'active_to' => 'nullable|date|after:active_from',
        ];

        // conditional rule
        if (!empty($this->active_to)){
            // \Illuminate\Validation\Concerns\ValidatesAttributes.php protected function compare($first, $second, $operator) will fail compare dates if second date is null on 'before' rule
            $rules['active_from'] .= '|before:active_to';
        }

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
            'private_url.required_if' => __('Private packs must have an URL.'),
            'private_url.regex' => __('URL should have lowercase letters, numbers and hyphens only.'),
        ];
    }
}
