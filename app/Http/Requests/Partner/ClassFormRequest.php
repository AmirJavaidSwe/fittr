<?php

namespace App\Http\Requests\Partner;

use App\Enums\ClassStatus;
use Illuminate\Validation\Rules\Enum;
use App\Rules\ArrayFieldExistsInDatabase;
use Illuminate\Foundation\Http\FormRequest;

class ClassFormRequest extends FormRequest
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
            'instructor_id' => ['required', 'array', new ArrayFieldExistsInDatabase('mysql_partner', 'users', 'id')],
            'class_type_id' => 'required|integer|exists:mysql_partner.class_types,id',
            'studio_id' => 'required|integer|exists:mysql_partner.studios,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'repeat_end_date' => 'required_if:does_repeat,true|nullable|date',
            'week_days' => 'required_if:does_repeat,true|nullable',
            'week_days.*' => 'in:0,1,2,3,4,5,6',
            'status' => ['required', new Enum(ClassStatus::class)],
            'is_off_peak' => 'boolean',
            'is_free' => 'boolean',
            'does_repeat' => 'boolean',
            'spaces' => 'nullable|integer|min:1|max:1000',
        ];

        return $rules;
    }
}
