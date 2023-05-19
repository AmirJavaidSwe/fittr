<?php

namespace App\Http\Requests\Shared;

use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;


class RoleRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'regex:"^[a-zA-Z0-9\s]+$"', 'max:191'],
            'permissions' => ['required', 'array', 'exists:permissions,id'],
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
            'title.regex' => __('Please use letters, numbers and space characters.'),
            'permissions.required' => __('Please set at least one permission for this role.'),
        ];
    }
}
