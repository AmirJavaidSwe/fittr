<?php

namespace App\Listeners;

use App\Events\SubscriptionChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SubscriptionChangedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SubscriptionChanged  $event
     * @return void
     */
    public function handle(SubscriptionChanged $event)
    {
        $old_subscription = $event->old_subscription;
        $new_subscription = $event->new_subscription;
        // do whatever
    }
}
