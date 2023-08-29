<?php

namespace App\Services\Store\Booking;

use App\Enums\BookingStatus;
use App\Traits\GenericHelper;
use App\Models\Partner\Booking;
use App\Events\BookingConfirmation;
use App\Models\Partner\ClassLesson;

class StoreBookingService
{
    use GenericHelper;


    public function store() {

        $request = request();

        $class = ClassLesson::with(['bookings' => function ($query) {
            $query->where('user_id', auth()->user()?->id)->active();
        }])
        ->active()
        ->find($request->class_id);

        $maxDaysBooking = session('business_settings.days_max_booking');

        $maxBookingStart = now(session('business_settings.timezone'))->startOfDay();
        $maxBookingEnd = $maxBookingStart->copy()
        ->when($maxDaysBooking, fn($date) => $date->addDays($maxDaysBooking-1))
        ->endOfDay()
        ->utc();
        $maxBookingStart = $maxBookingStart->utc();

        if(!$class) {
            return $this->redirectBackError(__('The class does not exist.'));
        }

        if(
            $maxDaysBooking
            && !(
                $maxBookingStart->lte($class->start_date)
                && $maxBookingEnd->gte($class->end_date)
            )
        ) {
            return $this->redirectBackError(__('The class cannot be booked.'));
        }

        if($class->bookings->count()) {
            return $this->redirectBackError(__('The class cannot be booked again.'));
        }

        //allow late booking, up to the end of class (TBD)
        if (now()->greaterThan($class->end_date)) {
            return $this->redirectBackError(__('This class can no longer be booked.'));
        }



        // TODO
        // $decision = $this->service->getReservationDecision($request);
        // going forward we will add another step for member selecting the seat or space and any other extras before we save the model


        if(!$this->classHasSpaceLeft($request->class_id)) {
            return $this->redirectBackError(__('This class can no longer be booked because it doesn\'t have required spaces left.'));
        }

        if($request->is_family_booking && count($request->familyBooking['family_member']['ids'])) {
            $familyDetails = $request->familyBooking;
            if($familyDetails['user_id'] != null) {
                $booking = Booking::create([
                    'class_id' => $request->class_id,
                    'user_id' => $request->user()->id,
                    'is_family_booking' => ($request->is_family_booking) ? 1 : 0,
                ]);
            }
            foreach($request->familyBooking['family_member']['ids'] as $id) {
                $booking = Booking::create([
                    'class_id' => $request->class_id,
                    'user_id' => $request->user()->id,
                    'is_family_booking' => ($request->is_family_booking) ? 1 : 0,
                    'family_member_id' => $id,
                ]);
            }
        } else {
            $booking = Booking::create([
                'class_id' => $request->class_id,
                'user_id' => $request->user()->id,
            ]);
        }

        $booking->load(['user', 'class.classType', 'class.instructor', 'class.studio.location']);

        event(new BookingConfirmation($booking));

        return $this->redirectBackSuccessWithSubdomain(__('The class has been booked'), 'ss.classes.index');
    }

    public function bookForOtherFamly() {

        $request = request();

        if(!$this->classHasSpaceLeft($request->class_id)) {
            return $this->redirectBackError(__('This class can no longer be booked because it doesn\'t have required spaces left.'));
        }

        foreach($request->members as $member) {
            $booking = Booking::create([
                'class_id' => $request->class_id,
                'user_id' => $request->user()->id,
                'is_family_booking' => 1,
                'family_member_id' => $member['is_parent'] == false ? $member['id'] : null,
            ]);
        }
        Booking::where('user_id', $request->user()->id)->where('class_id', $request->class_id)->active()->update(['is_family_booking' => 1]);

        return $this->redirectBackSuccessWithSubdomain('', 'ss.classes.index');

    }

    public function classHasSpaceLeft($id) {
        $request = request();
        $class = ClassLesson::with(['bookings' => function ($query) {
            $query->active();
        }])
        ->active()
        ->find($id);

        $spaces = $class->spaces;
        $bookingsCount = $class->bookings->count();

        $bookingFor = 0;

        if($request->is_family_booking && count($request->familyBooking['family_member']['ids'])) {
            $bookingFor += count($request->familyBooking['family_member']['ids']);
            if($request->familyBooking['user_id']) {
                $bookingFor++;
            }
        }
        if($request->has('is_new_family_booking') && count($request->members)) {
            $bookingFor = count($request->members);
        }

        return (($bookingsCount + $bookingFor) > $spaces) ? false : true;
    }

}
