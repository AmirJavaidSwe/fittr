<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
            'status' => ['required', 'boolean'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tx_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'tx_fixed_fee' => ['required', 'numeric', 'min:0', 'max:100'],
            'fee_annually' => ['required', 'numeric', 'min:0', 'max:999'],
            'fee_monthly' => ['required', 'numeric', 'min:0', 'max:999'],
            'monthly_fee_sites' => ['required', 'numeric', 'min:0', 'max:999'],
            'admin_users' => ['required', 'numeric', 'min:0'],
            'max_sites' => ['required', 'numeric', 'min:0'],
        ];
    }
}
