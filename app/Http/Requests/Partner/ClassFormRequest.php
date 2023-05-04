<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ClassStatus;

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
            'instructor_id' => 'required|integer|exists:mysql_partner.users,id',
            'class_type_id' => 'required|integer|exists:mysql_partner.class_types,id',
            'studio_id' => 'required|integer|exists:mysql_partner.studios,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'repeat_end_date' => 'required_if:does_repeat,true|nullable|date',
            'week_days' => 'required_if:does_repeat,true|nullable',
            'week_days.*' => 'in:0,1,2,3,4,5,6',
            'status' => ['required', new Enum(ClassStatus::class)],
            'is_off_peak' => 'boolean',
            'does_repeat' => 'boolean',
        ];

        return $rules;
    }
}
