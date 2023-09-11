<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Models\Partner\Booking;
use App\Http\Controllers\Controller;
use App\Services\Store\Booking\StoreBookingService;
use App\Services\Store\Waitlist\StoreWaitlistService;
use App\Services\Partner\WaiverValidationAndSaveService;
use App\Services\Store\Booking\StoreBookingCancellationService;

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
        if(!request()->has('request_data')) {
            request()->merge([
                'request_data' => request()->all()
            ]);
        }

        $storeBookingService = new StoreBookingService;

        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();

        $waiverSignNeeded = $storeBookingService->waiverSignNeeded();

        if(!empty($waiverValidation) || ($waiverSignNeeded['waiver'] != null && $waiverSignNeeded['waiver_sign_needed'] === true)) {
            return Inertia::render('Store/Classes/WaiverVerification', [
                "form_data" => request()->request_data,
                "waiver" => $waiverSignNeeded['waiver'],
                'submit_to_route' => 'ss.member.bookings.store',
                'page_title' => __('Waiver Verification Required'),
                'errors' => $waiverValidation
            ]);
        }

        WaiverValidationAndSaveService::saveWaiverAcceptanceData();

        return $storeBookingService->store();
    }

    public function cancel()
    {
        if(!request()->has('request_data')) {
            request()->merge([
                'request_data' => request()->all()
            ]);
        }

        $storeBookingCancellationService = new StoreBookingCancellationService;

        return $storeBookingCancellationService->cancel();

    }

    public function addToWaitlist()
    {
        if(!request()->has('request_data')) {
            request()->merge([
                'request_data' => request()->all()
            ]);
        }

        $storeWaitlistService = new StoreWaitlistService;
        return $storeWaitlistService->addToWaitlist();
    }

    public function removeFromWaitList()
    {
        $storeWaitlistService = new StoreWaitlistService;
        return $storeWaitlistService->removeFromWaitList();
    }


    public function bookForOtherFamly(Request $request) {

        $storeBookingService = new StoreBookingService;

        if(!request()->has('request_data')) {
            request()->merge([
                'request_data' => request()->all()
            ]);
        }

        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();

        $waiverSignNeeded = $storeBookingService->waiverSignNeeded();

        if(!empty($waiverValidation) || ($waiverSignNeeded['waiver'] != null && $waiverSignNeeded['waiver_sign_needed'] === true)) {

            return Inertia::render('Store/Classes/WaiverVerification', [
                "form_data" => request()->request_data,
                "waiver" => $waiverSignNeeded['waiver'],
                'page_title' => __('Waiver Verification Required'),
                'submit_to_route' => 'ss.member.bookings.other-famly',
                'errors' => $waiverValidation
            ]);
        }

        WaiverValidationAndSaveService::saveWaiverAcceptanceData();

        return $storeBookingService->bookForOtherFamly();

    }

    public function cancelForAllOrSelected(Request $request) {

        if(!request()->has('request_data')) {
            request()->merge([
                'request_data' => request()->all()
            ]);
        }

        $storeBookingCancellationService = new StoreBookingCancellationService;

        return $storeBookingCancellationService->cancelForAllOrSelected();
    }

}
