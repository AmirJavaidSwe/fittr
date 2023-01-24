<?php

namespace App\Listeners;

use App\Events\SubscriptionCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SubscriptionCancelledListener implements ShouldQueue
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
     * @param  \App\Events\SubscriptionCancelled  $event
     * @return void
     */
    public function handle(SubscriptionCancelled $event)
    {
        $subscription = $event->subscription;
        // do whatever
    }
}
