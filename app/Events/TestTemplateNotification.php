<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;

class TestTemplateNotification extends PartnerEvent
{
    use Dispatchable, InteractsWithSockets;

    /**
     * Required notification data
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(array $data)
    {
        parent::__construct();

        $this->data = $data;
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
