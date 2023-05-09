<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * Any policies that are explicitly mapped will take precedence over any potentially auto-discovered policies.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Partner\Amenity' => 'App\Policies\Partner\AmenityPolicy',
        'App\Models\Partner\Export' => 'App\Policies\Partner\ExportPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // The registerPolicies method of the AuthServiceProvider is now invoked automatically by the framework.
        // $this->registerPolicies();

        //

        // Gate::guessPolicyNamesUsing(function (string $modelClass) {
            // Return the name of the policy class for the given model...
        // });
    }

}
