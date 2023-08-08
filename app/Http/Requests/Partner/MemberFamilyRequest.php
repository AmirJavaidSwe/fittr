<?php

namespace App\Http\Requests\Partner;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberFamilyRequest extends FormRequest
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
        return  [
            'name' => 'required',
            'date_of_birth' => 'required|date',
            'profile_photo' => ['nullable', 'mimes:jpg,jpeg,png,,webp', 'max:2048']
        ];
    }
}
