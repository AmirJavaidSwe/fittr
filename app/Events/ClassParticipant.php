<?php

namespace App\Events;

use App\Models\Partner\ClassLesson;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;

class ClassParticipant extends PartnerEvent
{
    use Dispatchable, InteractsWithSockets;

    /**
     * The ClassLesson instance.
     *
     * @var \App\Models\ClassLesson
     */
    public $classLesson;
    public $data;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Partner\ClassLesson  $classLesson
     * @return void
     */
    public function __construct(ClassLesson $classLesson, array $data)
    {
        parent::__construct();

        $this->classLesson = $classLesson;
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
