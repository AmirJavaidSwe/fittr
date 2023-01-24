<?php

namespace App\Listeners;

use App\Events\SubscriptionStarted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SubscriptionStartedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SubscriptionStarted  $event
     * @return void
     */
    public function handle(SubscriptionStarted $event)
    {
        $subscription = $event->subscription;
        // do whatever
    }
}
