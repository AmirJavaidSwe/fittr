<?php

namespace App\Http\Requests\Partner;

use App\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PartnerUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() ? true : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'email' => [
                'required',
                'string',
                'max:191',
                Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($this->id)
            ],
            'password' => ['required_with:is_new', 'nullable', new Password, 'max:16'],
            'is_super' => 'boolean',
            'roles' => 'array|exists:roles,id|required_if:is_super,false',
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
            'password.required_with' => __('The password field is required.'),
            'roles.required_if' => __('Please select at least one role for the user.'),
        ];
    }
}
