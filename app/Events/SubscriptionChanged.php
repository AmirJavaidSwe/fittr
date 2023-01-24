<?php

namespace App\Events;

use App\Models\Subscription;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The previously cancelled subscription instance.
     *
     * @var \App\Models\Subscription
     */
    public $old_subscription;

    /**
     * The newly created subscription instance.
     *
     * @var \App\Models\Subscription
     */
    public $new_subscription;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Subscription  $old_subscription
     * @param  \App\Models\Subscription  $new_subscription
     * @return void
     */
    public function __construct(Subscription $old_subscription, Subscription $new_subscription)
    {
        $this->old_subscription = $old_subscription;
        $this->new_subscription = $new_subscription;
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
