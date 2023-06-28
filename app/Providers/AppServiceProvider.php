<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
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
    }
}
