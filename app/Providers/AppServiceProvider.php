<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Relation::enforceMorphMap([
            'user' => 'App\Models\User',
            'member' => 'App\Models\Partner\User',
            'pack' => 'App\Models\Partner\Pack',
            'location' => 'App\Models\Partner\Location',
        ]);

        VerifyEmail::createUrlUsing(function ($notifiable) {

            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
                false
            );

            $subdomain = session('business_settings.subdomain');
            $baseLink = '';

            if ($subdomain && ! $notifiable->source) {
                $baseLink = route('ss.home', ['subdomain' => $subdomain]);
            } else {
                $baseLink = route('root');
            }

            $verifyUrl = $baseLink . $verifyUrl;

            return $verifyUrl;
        });
    }
}
