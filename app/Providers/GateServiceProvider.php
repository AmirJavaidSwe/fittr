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

        $this->createGate('viewAny', AppUserSource::admin->name, 'dashboard', 'viewAny');
        $this->createGate('viewAny', AppUserSource::admin->name, 'partners-performance', 'viewAny');
        $this->createGate('viewAny', AppUserSource::admin->name, 'partner-management', 'viewAny');
        $this->createGate('create', AppUserSource::admin->name, 'partner-management', 'create');
        $this->createGate('update', AppUserSource::admin->name, 'partner-management', 'update');
        $this->createGate('view', AppUserSource::admin->name, 'partner-management', 'view');
        $this->createGate('loginAs', AppUserSource::admin->name, 'partner-management', 'loginAs');
        $this->createGate('viewAny', AppUserSource::admin->name, 'aws-instances', 'viewAny');
        $this->createGate('view', AppUserSource::admin->name, 'aws-instances', 'view');
        $this->createGate('showMetric', AppUserSource::admin->name, 'aws-instances', 'showMetric');
        $this->createGate('viewAny', AppUserSource::admin->name, 'settings', 'viewAny');
        $this->createGate('viewAny', AppUserSource::admin->name, 'admin-users', 'viewAny');
        $this->createGate('create', AppUserSource::admin->name, 'admin-users', 'create');
        $this->createGate('update', AppUserSource::admin->name, 'admin-users', 'update');
        $this->createGate('viewAny', AppUserSource::admin->name, 'packages', 'viewAny');
        $this->createGate('create', AppUserSource::admin->name, 'packages', 'create');
        $this->createGate('update', AppUserSource::admin->name, 'packages', 'update');
        $this->createGate('view', AppUserSource::admin->name, 'packages', 'view');
        $this->createGate('destroy', AppUserSource::admin->name, 'packages', 'destroy');
        $this->createGate('viewAny', AppUserSource::admin->name, 'roles', 'viewAny');
        $this->createGate('create', AppUserSource::admin->name, 'roles', 'create');
        $this->createGate('view', AppUserSource::admin->name, 'roles', 'view');
        $this->createGate('update', AppUserSource::admin->name, 'roles', 'update');
        $this->createGate('destroy', AppUserSource::admin->name, 'roles', 'destroy');
        
        $this->createGate('viewAny', AppUserSource::partner->name, 'dashboard', 'viewAny');
    }
}
