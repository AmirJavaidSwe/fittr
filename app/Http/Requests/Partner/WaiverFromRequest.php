<?php

namespace App\Http\Requests\Partner;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class WaiverFromRequest extends FormRequest
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
        $rules = [
            "title" => "required|string|max:255",
            "description" => "required|string",
            "questions" => "required|array",
            "questions.*.question" => "required|string|max:255",
            "questions.*.selectedQuestionType" => "required",
            "show_at" => "required",
        ];
        return $rules;
    }

    public function messages(): array
    {
        return [
            "show_at.unique" => 'Another waiver already exist for select :attribute type'
        ];

    }

    public function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description',
            "questions" => "question",
            "show_at" => "show at",
            'questions.*.question' => 'question',
            'questions.*.selectedQuestionType' => 'question type',
        ];
    }
}
