<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CommaSeparatedEmails implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!empty($value)) {
            $emails = explode(',', $value);
            $rules = [
                'email' => 'required|email',
            ];

            if (count($emails)) {
                $failed = false;
                foreach ($emails as $email) {
                    $data = [
                        'email' => trim($email)
                    ];
                    $validator = \Validator::make($data, $rules);
                    if ($validator->fails()) {
                        $failed = true;
                        break;
                    }
                }
            }

            if ($failed) {
                $fail('The :attribute field contains one or more invalid email address.');
            }
        }
    }
}
