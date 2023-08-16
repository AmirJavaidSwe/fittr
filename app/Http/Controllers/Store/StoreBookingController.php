<?php

namespace App\Http\Controllers\Store;

use App\Enums\BookingStatus;
use App\Events\BookingCancellation;
use App\Events\BookingConfirmation;
use App\Http\Controllers\Controller;
use App\Models\Partner\Booking;
use App\Models\Partner\ClassLesson;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreBookingController extends Controller
{
    protected $search;
    protected $per_page;
    protected $order_by;
    protected $order_dir;
    protected $member_id;

    // TODO
    // public function __construct(BookingService $service)
    // {
    //     $this->service = $service;
    // }

    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        $bookings = Booking::with(['class.classType', 'class.instructor', 'class.studio.location'])
        ->where('user_id', auth()->user()->id);
        if($request->is_parent == "false" && $request->search_member_id) {
            $bookings = $bookings->where('family_member_id', $request->search_member_id);
        }
        else if($request->is_parent == "true") {
            $bookings = $bookings->whereNull('family_member_id');
        }
        $bookings = $bookings->orderBy($this->order_by, $this->order_dir)
        ->when($this->search, function ($query) {
            $query->whereHas('class', function($subQuery) {
                $subQuery->where('title', 'LIKE', '%'.$this->search.'%');
            });
        })
        ->paginate($this->per_page)
        ->withQueryString();

        return Inertia::render('Store/MyBookings/Index', [
            "bookings" => $bookings,
            'search' => $this->search,
            'member_id' => $request->member_id,
            'search_member_id' => $request->search_member_id,
            'is_parent' => $request->is_parent,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('My bookings'),
        ]);
    }

    public function store(Request $request)
    {
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

        return $this->redirectBackSuccess('The class has been booked.');
    }

    public function cancel(Request $request)
    {
        $booking = Booking::with(['user', 'class.classType', 'class.instructor', 'class.studio.location'])->where('class_id', $request->class_id)->where('user_id', auth()->user()?->id)->active()->first();

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


        return $this->redirectBackSuccess('The booking has been cancelled.');
    }

    public function addToWaitlist(Request $request)
    {
        $class = ClassLesson::with(['waitlists' => function ($query) {
            $query->where('user_id', auth()->user()?->id);
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

        if($class->waitlists->count()) {
            return $this->redirectBackError(__('The class cannot be added to waitlist again.'));
        }

        if (now() > $class->start_date->subDay()) {
            return $this->redirectBackError(__('This class can no longer be added to waitlist.'));
        }

        Booking::create([
            'class_id' => $request->class_id,
            'user_id' => $request->user()->id,
            'status' => BookingStatus::get('waitlisted'),
        ]);

        return $this->redirectBackSuccess('The class has been added to waitlist.');
    }

    public function removeFromWaitList(Request $request)
    {
        $booking = Booking::where('class_id', $request->class_id)->where('user_id', auth()->user()?->id)->waitlisted()->first();

        if(!$booking) {
            return $this->redirectBackError('The booking not found.');
        }

        $booking->delete();

        return $this->redirectBackSuccess('The class has been removed from waitlist.');
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

    public function bookForOtherFamly(Request $request) {

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

        return redirect(route("ss.classes.index", ["subdomain" => $request->session()->get('business_settings')['subdomain']]))->with('flash_type', 'success')->with('flash_message', __('Success'))->with('flash_timestamp', time());

        // return Inertia::location());

        // return $this->redirectBackSuccess(
        //     'Booked Successfully!',
        //     route("ss.classes.index", ["subdomain" => $request->session()->get('business_settings')['subdomain']])
        // );
    }
    public function cancelForAllOrSelected(Request $request) {

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

            // $booking->update([
            //     'status' => BookingStatus::get('cancelled'),
            //     'cancelled_at' => now(),
            // ]);

            // event(new BookingCancellation($booking));

            $waitlist = Booking::with('user', 'class.classType', 'class.instructor', 'class.studio.location')->where('class_id', $request->class_id)->waitlisted()->skip($k)->first();

            if($waitlist) {
                $waitlists[] = $waitlist;
            }
            // if($waitlist) {
            //     $waitlist->update([
            //         'status' => BookingStatus::get('active'),
            //     ]);

            //     event(new BookingConfirmation($waitlist));
            // }
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
        return redirect(route("ss.classes.index", ["subdomain" => $request->session()->get('business_settings')['subdomain']]))->with('flash_type', 'success')->with('flash_message', __('The booking has been cancelled.'))->with('flash_timestamp', time());

        // return Inertia::location());

        // return $this->redirectBackSuccess(
        //     'Booked Successfully!',
        //     route("ss.classes.index", ["subdomain" => $request->session()->get('business_settings')['subdomain']])
        // );
    }

}
