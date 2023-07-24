<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;

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
            'title' => 'required|string|max:250',
            'location_id' => 'required|numeric',
            // 'ordering' => 'required|integer',
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
}
