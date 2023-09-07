<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class NotificationTemplateFormRequest extends FormRequest
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
            'from_name' => 'required|string|max:250',
            'from_email' => 'required|string|max:250',
            'subject' => 'required|string|max:250',
            'content' => 'required|string|max:2000',
            'content_plain' => 'required|string|max:2000',
            'unsubscribe' => 'required|boolean',
            'bypass' => 'required|boolean',
            'status' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ];

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'title' => 'title',
            'from_name' => 'from name',
            'from_email' => 'from email',
            'subject' => 'subject',
            'content' => 'content',
            'content_plain' => 'content plain',
            'unsubscribe' => 'unsubscribe',
            'bypass' => 'bypass',
            'status' => 'status',
            'notes' => 'notes',
        ];
    }
}
