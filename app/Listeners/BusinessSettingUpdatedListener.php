<?php

namespace App\Listeners;

use App\Events\BusinessSettingUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BusinessSettingUpdatedListener implements ShouldQueue
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
     * @param  \App\Events\BusinessSettingUpdated  $event
     * @return void
     */
    public function handle(BusinessSettingUpdated $event)
    {
        $business_id = $event->business_id;
        // do whatever
    }
}
