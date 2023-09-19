<?php

namespace App\Services\Store\Booking;

use Carbon\Carbon;
use App\Enums\BookingStatus;
use App\Traits\GenericHelper;
use App\Models\Partner\Waiver;
use App\Models\Partner\Booking;
use App\Models\Partner\UserWaiver;
use App\Events\BookingConfirmation;
use App\Models\Partner\ClassLesson;

class StoreBookingService
{
    use GenericHelper;

    public function store() {

        $request = collect(request()->request_data);

        $class = ClassLesson::with(['bookings' => function ($query) {
            $query->where('user_id', auth()->user()?->id)->active();
        }])
        ->active()
        ->find($request->get('class_id'));

        $maxDaysBooking = session('business_settings.days_max_booking');

        $maxBookingStart = now(session('business_settings.timezone'))->startOfDay();
        $maxBookingEnd = $maxBookingStart->copy()
        ->when($maxDaysBooking, fn($date) => $date->addDays($maxDaysBooking-1))
        ->endOfDay()
        ->utc();
        $maxBookingStart = $maxBookingStart->utc();

        if(!$class) {

            return $this->redirectBackErrorWithSubdomain(__('The class does not exist.'), (request()->returnTo ?? 'ss.classes.index'));
            // return $this->redirectBackError(__('The class does not exist.'));
        }

        if(
            $maxDaysBooking
            && !(
                $maxBookingStart->lte($class->start_date)
                && $maxBookingEnd->gte($class->end_date)
            )
        ) {
            return $this->redirectBackErrorWithSubdomain(__('The class cannot be booked.'), (request()->returnTo ?? 'ss.classes.index'));
            // return $this->redirectBackError(__('The class cannot be booked.'));
        }

        if($class->bookings->count()) {
            return $this->redirectBackErrorWithSubdomain(__('The class cannot be booked again.'), (request()->returnTo ?? 'ss.classes.index'));
            // return $this->redirectBackError(__('The class cannot be booked again.'));
        }

        //allow late booking, up to the end of class (TBD)
        if (now()->greaterThan($class->end_date)) {
            return $this->redirectBackErrorWithSubdomain(__('This class can no longer be booked.'), (request()->returnTo ?? 'ss.classes.index'));
            // return $this->redirectBackError(__('This class can no longer be booked.'));
        }



        // TODO
        // $decision = $this->service->getReservationDecision($request);
        // going forward we will add another step for member selecting the seat or space and any other extras before we save the model


        if(!$this->classHasSpaceLeft($request->get('class_id'))) {
            return $this->redirectBackErrorWithSubdomain(__('This class can no longer be booked because it doesn\'t have required spaces left.'), (request()->returnTo ?? 'ss.classes.index'));
            // return $this->redirectBackError(__('This class can no longer be booked because it doesn\'t have required spaces left.'));
        }

        if($request->get('is_family_booking') && count($request->get('familyBooking')['family_member']['ids'])) {
            $familyDetails = $request->get('familyBooking');
            if($familyDetails['user_id'] != null) {
                $booking = Booking::create([
                    'class_id' => $request->get('class_id'),
                    'user_id' => auth()->user()->id,
                    'is_family_booking' => ($request->get('is_family_booking')) ? 1 : 0,
                ]);
            }
            foreach($request->get('familyBooking')['family_member']['ids'] as $id) {
                $booking = Booking::create([
                    'class_id' => $request->get('class_id'),
                    'user_id' => auth()->user()->id,
                    'is_family_booking' => ($request->get('is_family_booking')) ? 1 : 0,
                    'family_member_id' => $id,
                ]);
            }
        } else {
            $booking = Booking::create([
                'class_id' => $request->get('class_id'),
                'user_id' => auth()->user()->id,
            ]);
        }

        $booking->load(['user', 'class.classType', 'class.instructor', 'class.studio.location']);

        event(new BookingConfirmation($booking));

        return $this->redirectBackSuccessWithSubdomain(__('The class has been booked'), 'ss.classes.index');
    }

    public function bookForOtherFamly() {

        $request = collect(request()->request_data);

        if(!$this->classHasSpaceLeft($request->get('class_id'))) {
            return $this->redirectBackError(__('This class can no longer be booked because it doesn\'t have required spaces left.'));
        }

        foreach($request->get('members') as $member) {
            $booking = Booking::create([
                'class_id' => $request->get('class_id'),
                'user_id' => auth()->user()->id,
                'is_family_booking' => 1,
                'family_member_id' => $member['is_parent'] == false ? $member['id'] : null,
            ]);
        }
        Booking::where('user_id', auth()->user()->id)->where('class_id', $request->get('class_id'))->active()->update(['is_family_booking' => 1]);

        return $this->redirectBackSuccessWithSubdomain('', 'ss.classes.index');

    }

    public function classHasSpaceLeft($id) {

        $request = collect(request()->request_data);

        $class = ClassLesson::with(['bookings' => function ($query) {
            $query->active();
        }])
        ->active()
        ->find($id);

        $spaces = $class->spaces;
        $bookingsCount = $class->bookings->count();

        $bookingFor = 0;

        if($request->get('is_family_booking') && count($request->get('familyBooking')['family_member']['ids'])) {
            $bookingFor += count($request->get('familyBooking')['family_member']['ids']);
            if($request->get('familyBooking')['user_id']) {
                $bookingFor++;
            }
        }
        if($request->has('is_new_family_booking') && count($request->get('members'))) {
            $bookingFor = count($request->get('members'));
        }

        return (($bookingsCount + $bookingFor) > $spaces) ? false : true;
    }

}
