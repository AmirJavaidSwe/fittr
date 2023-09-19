<?php

namespace App\Services\Store\Waitlist;

use App\Enums\BookingStatus;
use App\Traits\GenericHelper;
use App\Models\Partner\Booking;
use App\Models\Partner\ClassLesson;

class StoreWaitlistService
{
    use GenericHelper;
    public function addToWaitlist() {

        $request = request();

        $class = ClassLesson::with(['waitlists' => function ($query) {
            $query->where(function($q) {
                $q->where('user_id', auth()->user()?->id)
                ->orWhereIn('family_member_id', collect(request()->members)->where('is_parent', false)->pluck('id')->toArray());
            });
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
            return $this->redirectBackError(__('The class cannot be added to waitlist.'));
        }

        if($class->waitlists->count() == (count(auth()->user()->family) + 1)) {
            return $this->redirectBackError(__('The class cannot be added to waitlist again.'));
        }

        if (now() > $class->start_date->subDay()) {
            return $this->redirectBackError(__('This class can no longer be added to waitlist.'));
        }

        Booking::where(['status' => BookingStatus::get('waitlisted'),'user_id' => $request->user()->id,])->delete();
        // dd($waitlists);
        // dd(count(request()->members));
        if(isset(request()->members) && count(request()->members) > 0) {
            foreach(request()->members as $k => $member) {
                // dd($member);
                Booking::create([
                    'class_id' => $request->class_id,
                    'user_id' => $request->user()->id,
                    'family_member_id' => $member['is_parent'] ? null : $member['id'],
                    'status' => BookingStatus::get('waitlisted'),
                ]);
            }
        }
        return $this->redirectBackSuccessWithSubdomain('The class waiting list for you has been updated as per your choosen options.',  'ss.classes.index');
    }

    public function addSelfToWaitlist() {

        $request = request();

        $class = ClassLesson::with(['waitlists' => function ($query) {
            $query->where(function($q) {
                $q->where('user_id', auth()->user()?->id);
            });
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
            return $this->redirectBackError(__('The class cannot be added to waitlist.'));
        }

        if($class->waitlists->count() == (count(auth()->user()->family) + 1)) {
            return $this->redirectBackError(__('The class cannot be added to waitlist again.'));
        }

        if (now() > $class->start_date->subDay()) {
            return $this->redirectBackError(__('This class can no longer be added to waitlist.'));
        }

        Booking::where(['status' => BookingStatus::get('waitlisted'),'user_id' => $request->user()->id,])->delete();

        Booking::create([
            'class_id' => $request->class_id,
            'user_id' => $request->user()->id,
            'status' => BookingStatus::get('waitlisted'),
        ]);

        return $this->redirectBackSuccessWithSubdomain('You have been removed from the class waitlist.',  'ss.classes.index');
    }
    public function removeFromWaitlist() {

        $request = request();

        $booking = Booking::where('class_id', $request->class_id)
        ->where('user_id', auth()->user()?->id)
        ->orWhereIn('family_member_id', collect(request()->members)->where('is_parent', false)->pluck('id')->toArray())
        ->waitlisted()->get();

        if($booking->isEmpty()) {
            return $this->redirectBackError('The booking not found.');
        }

        $booking->each(function ($item, $key) {
            $item->delete();
        });

        $message = isset(request()->members) && count(request()->members) ? 'The class has been removed from waitlist for selected memebers.' : 'The class has been removed from waitlist.';

        return $this->redirectBackSuccessWithSubdomain($message, 'ss.classes.index');
    }
}
