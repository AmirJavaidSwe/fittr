<?php

namespace App\Listeners;

use App\Events\BookingConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class BookingConfirmationListener extends PartnerListener implements ShouldQueue
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
     * @param  \App\Events\BookingConfirmation  $event
     * @return void
     */
    public function handle(BookingConfirmation $event)
    {
        //parent method will set 1 public prop on this class: array $business_settings
        $this->setPartnerConnection($event);
        $booking = $event->booking;
        $business_settings = $this->business_settings;

        Mail::send('emails.booking_confirmation', ['booking' => $booking, 'settings' => $business_settings], function($message) use ($booking) {
            $message->to($booking->user->email);
            $message->subject('Booking Confirmation');
        });
    }
}
