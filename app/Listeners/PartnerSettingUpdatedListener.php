<?php

namespace App\Listeners;

use App\Events\PartnerSettingUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PartnerSettingUpdatedListener implements ShouldQueue
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
     * @param  \App\Events\PartnerSettingUpdated  $event
     * @return void
     */
    public function handle(PartnerSettingUpdated $event)
    {
        $partner_id = $event->partner_id;
        // do whatever
    }
}
