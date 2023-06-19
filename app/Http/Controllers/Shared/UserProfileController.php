<?php

namespace App\Http\Controllers\Shared;

use App\Models\User;
use App\Enums\AppUserSource;
use Illuminate\Http\Request;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController as JetstreamUserProfileController;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Features;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserProfileController extends JetstreamUserProfileController
{
    /**
     * Show the general profile settings screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {
        $this->validateTwoFactorAuthenticationState($request);

        return Jetstream::inertia()->render($request, 'Profile/Show', [
            'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
            'sessions' => $this->sessions($request)->all(),
            'page_title' => __('Profile management'),
            'header' => __('Profile management'),
        ]);
    }

    public function googleAuth()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('root');
        }

        $name = $user->getName();
        $email = $user->getEmail();

        $user = User::where('email', $email)->first();
        if(!$user){
            $special_chars = ['!', '@', '#', '$', '%', '^', '&', '*'];
            $password = Str::random(7).$special_chars[array_rand($special_chars)];
            $msg_pass = __('Your password is: :password', ['password' => $password]);
            session()->flash('flash.banner', $msg_pass);
            $user = User::create([
                'is_super' => true,
                'name' => $name,
                'email' => $email,
                'role' => AppUserSource::partner->name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
        }
        // note: redirection to dashboard route for partner will hit ConnectPartnerDatabase middleware, which may redirect user to onboarding flow when user has no business.

        Auth::login($user);

        return redirect()->intended(route($user->dashboard_route));
    }
}
