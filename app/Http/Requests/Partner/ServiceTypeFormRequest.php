<?php

namespace App\Http\Requests\Partner;

use App\Enums\StateType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ServiceTypeFormRequest extends FormRequest
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
            'description' => 'nullable|max:65355',
            'status' => ['required', new Enum(StateType::class)],
        ];

        return $rules;
    }
}
