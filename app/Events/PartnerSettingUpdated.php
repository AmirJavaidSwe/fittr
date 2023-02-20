<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PartnerSettingUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Partner user id.
     *
     * @var 
     */
    public $partner_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($partner_id)
    {
        $this->partner_id = $partner_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
