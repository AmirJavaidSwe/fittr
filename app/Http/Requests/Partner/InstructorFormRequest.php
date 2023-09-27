<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class InstructorFormRequest extends FormRequest
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
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'phone' => 'nullable|regex:"^[0-9]+$"',
            'email' => 'required|email|unique:mysql_partner.users,email,'.$this->instructor?->id,
            'old_profile_image' => 'boolean',
            'profile_image' => [
                'nullable',
                'exclude_if:old_profile_image,true',
                File::image()
                ->min(1) //KB
                ->max(20 * 1024) //KB
                ->dimensions(Rule::dimensions([1920, 1280])),
            ],
            'profile_description' => 'nullable|max:5000',
        ];

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'profile_image' => 'photo',
            'profile_description' => 'description',
        ];
    }
}
