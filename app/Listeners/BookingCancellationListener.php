<?php

namespace App\Listeners;

use App\Events\BookingConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingCancellationListener implements ShouldQueue
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
        $booking = $event->booking;

        $timezone = session('business_seetings.timezone') ?? config('app.timezone');

        $booking->class->start_date = $booking->class->start_date->tz($timezone);
        $booking->class->end_date = $booking->class->end_date->tz($timezone);

        Mail::send('emails.booking_cancellation', ['booking' => $booking, 'settings' => session('business_seetings')], function($message) use ($booking) {
            $message->to($booking->user->email);
            $message->subject('Booking Confirmation');
        });
    }
}
