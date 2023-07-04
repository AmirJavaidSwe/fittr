<?php

namespace App\Actions\Fortify;

use App\Enums\PartnerUserRole;
use App\Models\User;
use App\Models\Partner\User as Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        // if we have config('subdomain') and database.connections.mysql_partner => incoming request to register is coming from partner subdomain

        if(!empty(config('subdomain')) && !empty(config('database.connections.mysql_partner'))){
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:mysql_partner.users'],
                'password' => $this->passwordRules(),
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            ])->validate();

            return Member::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'role' => PartnerUserRole::get('member'),
                'password' => Hash::make($input['password']),
            ]);
        }
        
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'source' => 'partner',
            'is_super' => true,
            'password' => Hash::make($input['password']),
        ]);
    }
}
