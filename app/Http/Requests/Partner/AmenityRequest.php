<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;

class AmenityRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'contents' => 'required|string',
            'ordering' => 'required|integer',
            'studio_id' => 'required|integer',
            'status' => 'boolean'
        ];
    }
}
