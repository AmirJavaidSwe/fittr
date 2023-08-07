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

        return Inertia::render('Store/MyBookings/Index', [
            'bookings' => Booking::with(['class.classType', 'class.instructor', 'class.studio.location'])
            ->orderBy($this->order_by, $this->order_dir)
            ->when($this->search, function ($query) {
                $query->whereHas('class', function($subQuery) {
                    $subQuery->where('title', 'LIKE', '%'.$this->search.'%');
                });
            })
            ->paginate($this->per_page)
            ->withQueryString(),
            'search' => $this->search,
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

        $booking = Booking::create([
            'class_id' => $request->class_id,
            'user_id' => $request->user()->id,
        ]);

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

        if (now()->lte($class->start_date->subDay())) {
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
}
