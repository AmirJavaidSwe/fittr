<?php

namespace App\Providers;

use App\Events\BookingCancellation;
use App\Events\BookingConfirmation;
use App\Events\BusinessSettingUpdated;
use App\Events\SubscriptionCancelled;
use App\Events\SubscriptionChanged;
use App\Events\SubscriptionStarted;
use App\Events\OrderPaid;
use App\Listeners\BookingCancellationListener;
use App\Listeners\BookingConfirmationListener;
use App\Listeners\BusinessSettingUpdatedListener;
use App\Listeners\SubscriptionCancelledListener;
use App\Listeners\SubscriptionChangedListener;
use App\Listeners\SubscriptionStartedListener;
use App\Listeners\OrderPaidListener;
use App\Models\Partner\ClassLesson;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Observers\ClassLessonObserver;

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
        BookingConfirmation::class => [
            BookingConfirmationListener::class,
        ],
        BookingCancellation::class => [
            BookingCancellationListener::class,
        ],
        OrderPaid::class => [
            OrderPaidListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        ClassLesson::observe(ClassLessonObserver::class);
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
