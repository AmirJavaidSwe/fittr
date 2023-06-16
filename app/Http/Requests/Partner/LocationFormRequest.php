<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class LocationFormRequest extends FormRequest
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
            'brief' => 'nullable|string|max:500',
            'manager_id' => 'required|numeric',
            'address_line_1' => 'nullable|string|max:250',
            'address_line_2' => 'nullable|string|max:250',
            'country_id' => 'required|numeric',
            'city' => 'nullable|string|max:250',
            'postcode' => 'nullable|string|max:50',
            'map_latitude' => 'nullable|string|max:50',
            'map_longitude' => 'nullable|string|max:50',
            'tel' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:250',
            'amenity_ids' => 'nullable',
            'status' => 'nullable|boolean',
            'image' => [
                'nullable',
                File::image()
                ->min(1) //KB
                ->max(20 * 1024) //KB
                ->dimensions(Rule::dimensions([1920, 1280])),
            ],
            'studio_ids' => 'nullable',
        ];

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'brief' => 'description',
            'manager_id' => 'general manager',
            'address_line_1' => 'address line 1',
            'address_line_2' => 'address line 2',
            'country_id' => 'country',
            'map_latitude' => 'latitude',
            'map_longitude' => 'longitude',
            'tel' => 'phone',
            'amenity_ids' => 'amenities',
            'studio_ids' => 'studios',
        ];
    }
}
