<?php

namespace App\Http\Controllers\Shared;

use App\Models\Partner\User as PartnerUser;
use App\Models\User;
use App\Enums\AppUserSource;
use App\Enums\PartnerUserRole;
use Illuminate\Http\Request;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController as JetstreamUserProfileController;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Features;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Traits\GenericHelper;

class UserProfileController extends JetstreamUserProfileController
{
    use GenericHelper;

    /**
     * Show the general profile settings screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {
        $this->validateTwoFactorAuthenticationState($request);

        return Jetstream::inertia()->render(
            $request,
            empty(config('subdomain')) ? 'Profile/Show' : 'Store/Profile',
            [
                'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
                'sessions' => $this->sessions($request)->all(),
                'page_title' => __('Profile management'),
                'header' => __('Profile management'),
            ]
        );
    }
    
    //get requests to this route may come from app. (admins and partners app) of from any connected service store subdomain
    //google will not accept any requests from service store subdomains, only app.fittr.tech
    // get /auth/google
    public function googleRedirect(Request $request)
    {
        //calls from subdomains will have 'subdomain' config set
        if(!empty(config('subdomain'))){
            //redirect to main app, to exact this same route but with subdomain query attached:
            return redirect()->away(config('app.url').'/auth/google?subdomain='.config('subdomain.name'));
        }

        // $request will have subdomain query present from above
        if($request->filled('subdomain')){
            //save auth_subdomain to session, so that main app could determine the auth flow on callback
            session()->flash('auth_subdomain', $request->subdomain);
        }

        return Socialite::driver('google')->redirect();
    }
    
    // get /auth/google-callback
    public function googleAuth(Request $request)
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('root');
        }

        // incoming request to authenticate originally came from service store domain:
        if (session()->has('auth_subdomain')) {
            return $this->redirectToSubdomain($user, session()->pull('auth_subdomain'));
        }

        $name = $user->getName();
        $email = $user->getEmail();

        $user = User::where('email', $email)->first();
        if(!$user){
            $password = GenericHelper::generateString('password', 10);
            session()->flash('flash.banner', __('Your password is: :password', ['password' => $password]));
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

    // called on google callback, when main app has session item auth_subdomain - service store subdomains only
    public function redirectToSubdomain($user, $subdomain)
    {
        //make post request from main app to subdomain
        //because app.key is common, we can add a signature to request and then verify it, making sure that POST request to /auth/google-callback came from our app
        //this security measure is super important, CSRF token for this route is off, if we don't check signnature anyone can make post request with user email to get in
        $signature = hash_hmac('sha256', 'login'.$subdomain, config('app.key'));
        $protocol = config('app.env') === 'production' ? 'https://' : 'http://';
        $url = $protocol.$subdomain.'.'.config('app.domain').'/auth/google-callback';

        //make request to subdomain POST /auth/google-callback (processSubdomainRequest()) 
        $response = Http::post($url, [
            'subdomain' => $subdomain,
            'signature' => $signature,
            'user' => $user,
        ]);

        $json = $response->json();

        if(!$response->ok() || empty($json['redirect'])){
            abort(401);
        }

        // if response is a success, redirect away to subdomain
        // $json['redirect'] has signed link for subdomain user to login. The link leads to service store home page,
        // StorePublicController@index will examine the presence of encrypted uid query, making sure link has valid signature
        return redirect()->away($json['redirect']);
    }

    // POST /auth/google-callback
    public function processSubdomainRequest(Request $request)
    {
        $user = $request->user;
        $subdomain = $request->subdomain;
        $signature = hash_hmac('sha256', 'login'.$subdomain, config('app.key'));
        $hash_equals = hash_equals($request->signature, $signature);
        $name = $user['name'] ?? null;
        $email = $user['email'] ?? null;
        if($hash_equals !== true || empty($name) || empty($email)){
            abort(401);
        }
        $user = PartnerUser::where('email', $email)->first();
        if(!$user){
            try {
                $user = PartnerUser::create([
                    'name' => $name,
                    'email' => $email,
                    'role' => PartnerUserRole::get('member'),
                    'password' => Hash::make(GenericHelper::generateString('password', 10)),
                    'email_verified_at' => now(),
                ]);
            } catch (\Illuminate\Database\QueryException $exception) {
                $errorInfo = $exception->errorInfo;
            }
        }
        if(!$user){
            abort(401);
        }
        
        // create short live signed url to authenticate
        $redirect = URL::temporarySignedRoute('ss.home', now()->addSeconds(5), ['subdomain' => $subdomain, 'uid' => Crypt::encryptString($user->id)]);

        return response()->json(['redirect' => $redirect]);
    }
}
