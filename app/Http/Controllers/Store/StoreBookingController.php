<?php

namespace App\Http\Controllers\Store;

use App\Enums\BookingStatus;
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
            'page_title' => __('My boookings'),
        ]);
    }

    public function store(Request $request)
    {

        $class = ClassLesson::with(['bookings' => function ($query) {
            $query->where('user_id', auth()->user()?->id)->active();
        }])->find($request->class_id);

        if($class->bookings->count()) {
            return $this->redirectBackError('The class cannot be booked again.');
        }

        if($class->start_date && now()->gte($class->start_date)) {
            return $this->redirectBackError('The class cannot be booked after it has been started.');
        }

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
        $booking = Booking::with('class', 'user')->where('class_id', $request->class_id)->where('user_id', auth()->user()?->id)->active()->first();

        if(!$booking) {
            return $this->redirectBackError('The booking not found.');
        }

        if(now()->gte($booking->class?->start_date)) {
            return $this->redirectBackError('The booking cannot be cancelled after the class has been started.');
        }

        if($booking->status == BookingStatus::CANCELLED->value) {
            return $this->redirectBackError('The booking cannot be cancelled again.');
        }

        $booking->update(['status' => BookingStatus::CANCELLED->value]);

        return $this->redirectBackSuccess('The booking has been cancelled.');
    }
}
