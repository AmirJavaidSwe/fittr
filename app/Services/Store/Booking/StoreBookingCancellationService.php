<?php

namespace App\Services\Store\Booking;

use App\Enums\BookingStatus;
use App\Traits\GenericHelper;
use App\Models\Partner\Booking;
use App\Events\BookingCancellation;
use App\Events\BookingConfirmation;

class StoreBookingCancellationService
{
    use GenericHelper;

    public function cancel() {

        $request = request();

        $booking = Booking::with(['user', 'class.classType', 'class.instructor', 'class.studio.location'])
                   ->where('class_id', $request->class_id)
                   ->where('user_id', auth()->user()?->id)
                   ->where('id', $request->id)->active()->first();

        if(!$booking) {
            return $this->redirectBackError('The booking not found.');
        }

        if(now()->gte($booking->class?->start_date)) {
            return $this->redirectBackError('The booking cannot be cancelled after the class has been started.');
        }

        if($booking->status == BookingStatus::get('cancelled')) {
            return $this->redirectBackError('The booking cannot be cancelled again.');
        }


        $booking->update([
            'status' => BookingStatus::get('cancelled'),
            'cancelled_at' => now(),
        ]);

        event(new BookingCancellation($booking));

        $waitlist = Booking::with('user', 'class.classType', 'class.instructor', 'class.studio.location')->where('class_id', $request->class_id)->waitlisted()->first();

        if($waitlist) {
            $waitlist->update([
                'status' => BookingStatus::get('active'),
            ]);

            event(new BookingConfirmation($waitlist));
        }

        return $this->redirectBackSuccess(__('The booking has been cancelled.'));

    }

    public function cancelForAllOrSelected() {

        $request = request();

        $bookings = [];
        $waitlists = [];

        foreach($request->ids_cancellation as $k => $member) {

            $booking = Booking::with(['user', 'class.classType', 'class.instructor', 'class.studio.location'])->where('class_id', $request->class_id)
            ->where('user_id', auth()->user()?->id);

            if(!$member['is_parent']) {
                $booking = $booking->where('family_member_id', $member['id']);
            }
            $booking = $booking->active()->first();

            if(!$booking) {
                return $this->redirectBackError('The booking not found.');
            }

            if(now()->gte($booking->class?->start_date)) {
                return $this->redirectBackError('The booking cannot be cancelled after the class has been started.');
            }

            if($booking->status == BookingStatus::get('cancelled')) {
                return $this->redirectBackError('The booking cannot be cancelled again.');
            }

            $bookings[] = $booking;

            $waitlist = Booking::with('user', 'class.classType', 'class.instructor', 'class.studio.location')->where('class_id', $request->class_id)->waitlisted()->skip($k)->first();

            if($waitlist) {
                $waitlists[] = $waitlist;
            }
        }
        foreach($bookings as $key => $booking) {
            $booking->update([
                'status' => BookingStatus::get('cancelled'),
                'cancelled_at' => now(),
            ]);
            event(new BookingCancellation($booking));
        }
        foreach($waitlists as $key => $waitlist) {
            $waitlist->update([
                'status' => BookingStatus::get('active'),
            ]);
            event(new BookingConfirmation($waitlist));
        }
        return $this->redirectBackSuccessWithSubdomain( 'The booking has been cancelled.', 'ss.classes.index');
    }
}
