<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class StudioFormRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:250',
                Rule::unique('App\Models\Partner\Studio')->where(fn (Builder $query) => $query->where('location_id', $this->location_id))->ignore($this->studio?->id),
            ],
            'location_id' => [
                'nullable',
                'exists:mysql_partner.locations,id'
            ],
        ];

        if($this->class_type_studios) {
            $rules += [
                'class_type_studios.*.class_type_id' => 'required|distinct|numeric',
                'class_type_studios.*.spaces' => 'required|numeric|min:1|max:1000',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'location_id' => 'location',
            'class_type_studios.*.class_type_id' => 'class type',
            'class_type_studios.*.spaces' => 'number of places',
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
            'title.unique' => __('The :attribute has already been taken for selected location.'),
        ];
    }
}
