<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderPaidListener extends PartnerListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        $this->setPartnerConnection($event);
        $order = $event->order;

        //TEMP send email without template
        \Illuminate\Support\Facades\Mail::send([], [], function (\Illuminate\Mail\Message $message) use ($order) {
            $message
            ->from(config('mail.from.address'))
            ->to($order->user->email)
            ->subject('Order paid')
            ->text('Order paid!')
            ->html('<p>Order paid!</p>');
        });
    }
}
