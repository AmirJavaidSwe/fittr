<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Fortify::ignoreRoutes();

        $host = request()->host();
        Config::set('fortify.domain', $host);

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $url = empty(config('subdomain')) ?
                    route($request->user()?->dashboard_route) :
                    route($request->user()?->dashboard_route, ['subdomain' => config('subdomain.name')]);

                return redirect()->intended($url);
            }
        });

        $this->app->instance(TwoFactorLoginResponse::class, new class implements TwoFactorLoginResponse {
            public function toResponse($request)
            {
                $url = empty(config('subdomain')) ?
                    route($request->user()?->dashboard_route) :
                    route($request->user()?->dashboard_route, ['subdomain' => config('subdomain.name')]);

                return redirect()->intended($url);
            }
        });

        // RouteServiceProvider::HOME will redirect to /dashboard url,
        // this will handle partners and members; fittr admins and partner instructors won't register on their own
        // therefore, no need to override after registration redirect, method below is for reference

        // $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
        //     public function toResponse($request)
        //     {
        //         return redirect()->intended();
        //     }
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
