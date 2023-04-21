<?php

namespace App\Providers;

use App\Events\BusinessSettingUpdated;
use App\Events\SubscriptionCancelled;
use App\Events\SubscriptionChanged;
use App\Events\SubscriptionStarted;
use App\Listeners\BusinessSettingUpdatedListener;
use App\Listeners\SubscriptionCancelledListener;
use App\Listeners\SubscriptionChangedListener;
use App\Listeners\SubscriptionStartedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SubscriptionCancelled::class => [
            SubscriptionCancelledListener::class,
        ],
        SubscriptionChanged::class => [
            SubscriptionChangedListener::class,
        ],
        SubscriptionStarted::class => [
            SubscriptionStartedListener::class,
        ],
        BusinessSettingUpdated::class => [
            BusinessSettingUpdatedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
