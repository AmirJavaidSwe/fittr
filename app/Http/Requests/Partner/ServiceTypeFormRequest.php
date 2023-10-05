<?php

namespace App\Http\Requests\Partner;

use App\Enums\StateType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;

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
            'title' => 'required|string|max:255|unique:mysql_partner.service_types,title,'.$this->servicetype?->id,
            'description' => 'nullable|max:65355',
            'status' => ['required', new Enum(StateType::class)],
            'old_image' => 'boolean',
            'image' => [
                'nullable',
                'exclude_if:old_image,true',
                File::image()
                ->min(1) //KB
                ->max(2 * 1024) //KB
                ->dimensions(Rule::dimensions([1920, 1920])),
            ],
        ];

        return $rules;
    }
}
