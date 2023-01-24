<?php

namespace App\Http\Requests\Partner;

use App\Enums\Subscription as SubscriptionEnum;
use App\Models\Subscription;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Subscription::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'period' => [
                'required',
                new Enum(SubscriptionEnum::class),
            ],
        ];
    }
}
