<?php

namespace App\Events;

use App\Models\Partner\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class BookingCancellation extends PartnerEvent
{
    use Dispatchable, InteractsWithSockets;

    /**
     * The Booking instance.
     *
     * @var \App\Models\Booking
     */
    public $booking;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Partner\Booking  $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        parent::__construct();

        $this->booking = $booking;
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
