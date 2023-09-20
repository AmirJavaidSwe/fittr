<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\PasswordValidationRules;
use Laravel\Fortify\Http\Controllers\NewPasswordController as FortifyNewPasswordController;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;
use App\Traits\GenericHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class NewPasswordController extends FortifyNewPasswordController
{
    use GenericHelper, PasswordValidationRules;

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function broker(): PasswordBroker
    {
        $config = GenericHelper::isMainDomain() ? config('fortify.passwords') : config('fortify.passwords_subdomain');

        return Password::broker($config);
    }

    public function passwordCreation(Request $request)
    {
        $validated = $request->validate(['password' => $this->passwordRules()]);

        $user = $request->user();

        // Abort if user already created password previously because this feature must be strictly used for one time only for security purposes.
        if ($user->password) {
            abort(403);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return $this->redirectBackSuccess('You new password has been created.');
    }
}
