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

        $this->createGate('viewAny', AppUserSource::get('admin'), 'dashboard', 'viewAny');
        $this->createGate('viewAny', AppUserSource::get('admin'), 'partners-performance', 'viewAny');
        $this->createGate('viewAny', AppUserSource::get('admin'), 'partner-management', 'viewAny');
        $this->createGate('create', AppUserSource::get('admin'), 'partner-management', 'create');
        $this->createGate('update', AppUserSource::get('admin'), 'partner-management', 'update');
        $this->createGate('view', AppUserSource::get('admin'), 'partner-management', 'view');
        $this->createGate('loginAs', AppUserSource::get('admin'), 'partner-management', 'loginAs');
        $this->createGate('viewAny', AppUserSource::get('admin'), 'aws-instances', 'viewAny');
        $this->createGate('view', AppUserSource::get('admin'), 'aws-instances', 'view');
        $this->createGate('showMetric', AppUserSource::get('admin'), 'aws-instances', 'showMetric');
        $this->createGate('viewAny', AppUserSource::get('admin'), 'settings', 'viewAny');
        $this->createGate('viewAny', AppUserSource::get('admin'), 'admin-users', 'viewAny');
        $this->createGate('create', AppUserSource::get('admin'), 'admin-users', 'create');
        $this->createGate('update', AppUserSource::get('admin'), 'admin-users', 'update');
        $this->createGate('viewAny', AppUserSource::get('admin'), 'packages', 'viewAny');
        $this->createGate('create', AppUserSource::get('admin'), 'packages', 'create');
        $this->createGate('update', AppUserSource::get('admin'), 'packages', 'update');
        $this->createGate('view', AppUserSource::get('admin'), 'packages', 'view');
        $this->createGate('destroy', AppUserSource::get('admin'), 'packages', 'destroy');
        $this->createGate('viewAny', AppUserSource::get('admin'), 'roles', 'viewAny');
        $this->createGate('create', AppUserSource::get('admin'), 'roles', 'create');
        $this->createGate('view', AppUserSource::get('admin'), 'roles', 'view');
        $this->createGate('update', AppUserSource::get('admin'), 'roles', 'update');
        $this->createGate('destroy', AppUserSource::get('admin'), 'roles', 'destroy');
        
        $this->createGate('viewAny', AppUserSource::get('partner'), 'dashboard', 'viewAny');
    }
}
