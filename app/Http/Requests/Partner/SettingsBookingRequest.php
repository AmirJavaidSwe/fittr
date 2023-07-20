<?php

namespace App\Http\Requests\Partner;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class SettingsBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->is_partner;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'days_max_booking' => array_merge([
                'required_if:days_max_booking_on,true',
                'exclude_if:days_max_booking_on,false'
            ], SettingKey::days_max_booking->rules()),
            'days_max_timetable' => array_merge([
                'required_if:days_max_timetable_on,true',
                'exclude_if:days_max_timetable_on,false'
            ], SettingKey::days_max_timetable->rules()),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'days_max_booking.required_if' => __('Please, provide a number for the limit.'),
            'days_max_booking.min' => __('The limit must be at least :min.'),
            'days_max_booking.max' => __('The limit must not be greater than :max.'),
            'days_max_timetable.required_if' => __('Please, provide a number for the limit.'),
            'days_max_timetable.min' => __('The limit must be at least :min.'),
            'days_max_timetable.max' => __('The limit must not be greater than :max.'),
        ];
    }
}
