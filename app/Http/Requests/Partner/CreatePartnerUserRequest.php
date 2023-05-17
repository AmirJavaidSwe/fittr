<?php

namespace App\Http\Requests\Partner;

use App\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreatePartnerUserRequest extends FormRequest
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
                Rule::unique('users', 'email')->whereNull('deleted_at')
            ],
            'password' => ['required', 'string', new Password, 'max:16']
        ];
    }
}
