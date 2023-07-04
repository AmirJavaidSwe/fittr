<?php

namespace App\Http\Controllers\Auth;

use Laravel\Fortify\Http\Controllers\PasswordResetLinkController as FortifyPasswordResetLinkController;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;
use App\Traits\GenericHelper;

class PasswordResetLinkController extends FortifyPasswordResetLinkController
{
    use GenericHelper;
    
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
}
