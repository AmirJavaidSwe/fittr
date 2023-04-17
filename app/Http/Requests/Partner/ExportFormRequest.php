<?php

namespace App\Http\Requests\Partner;

use App\Enums\ExportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
// use Illuminate\Validation\Rules\Enum;

class ExportFormRequest extends FormRequest
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
     * NOTE: Enum rule only works with backed enums.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules =  [
            'export_type' => ['required', Rule::in(ExportType::all())],
            'filters' => 'nullable|array',
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
            'export_type.in' => __('Unsupported export type'),
        ];
    }
}
