<?php

namespace App\Providers;

use App\Traits\GateHelper;
use App\Enums\AppUserSource;
use Illuminate\Support\ServiceProvider;

class GateServiceProvider extends ServiceProvider
{

    use GateHelper;
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Custom Gate Policies for authorization checking

        $this->createGate('viewAny', AppUserSource::admin->name, 'dashboard', "viewAny");
        $this->createGate('viewAny', AppUserSource::partner->name, 'dashboard', "viewAny");
        $this->createGate('view', AppUserSource::admin->name, 'dashboard', 'view');
    }
}
