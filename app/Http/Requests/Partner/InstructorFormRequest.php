<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;

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
        ];

        return $rules;
    }
}
