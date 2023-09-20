<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Fortify\Contracts\VerifyEmailResponse;
use Laravel\Fortify\Http\Requests\VerifyEmailRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\VerifyEmailResponse
     */
    public function __invoke(Request $request)
    {

        $alreadyLoggedIn = false;

        if (! $request->user()) {
            // web or store already configured earlier on subdomain init
            $fortifyGuard = config('fortify.guard');

            // Login users for validating signed urls and also let them create their password by auto login
            auth()->guard($fortifyGuard)->loginUsingId($request->route('id'));

            $alreadyLoggedIn = false;
        } else {
            $alreadyLoggedIn = true;
        }

        $authorize = true;

        if (! hash_equals((string) $request->user()?->getKey(), (string) $request->route('id'))) {
            $authorize = false;
        }

        if (! hash_equals(sha1($request->user()?->getEmailForVerification()), (string) $request->route('hash'))) {
            $authorize = false;
        }

        if (! $authorize) {
            if (! $alreadyLoggedIn) {
                auth()->guard($fortifyGuard)->logout(); // Logout on altered url
            }
            abort(401);
        }

        if ($request->user()->hasVerifiedEmail()) {
            if (! $alreadyLoggedIn) {
                auth()->guard($fortifyGuard)->logout(); // Force logout if attempt to verify again
            }

            return app(VerifyEmailResponse::class);
        }

        if ($request->user()->markEmailAsVerified()) {
            session()->flash('extra', ['show_password_creation' => true]); // Show password creation for first time only
            event(new Verified($request->user()));
        }

        return app(VerifyEmailResponse::class);
    }
}
