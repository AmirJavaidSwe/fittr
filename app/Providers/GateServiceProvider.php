<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Traits\GateHelper;

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

        $this->createGate('view', 'admin', 'dashboard', 'View');
    }
}
