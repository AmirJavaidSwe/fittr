<?php

namespace App\Http\Controllers\Store;

use App\Enums\PackType;
use App\Enums\StateType;
use App\Enums\StripePriceType;
use App\Http\Controllers\Controller;
use App\Models\Partner\ClassType;
use App\Models\Partner\Location;
use App\Models\Partner\Membership;
use App\Models\Partner\ServiceType;
use App\Services\Store\StoreMembershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

use Illuminate\Database\Eloquent\Builder;

class StoreMembershipController extends Controller
{
    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;

    public function __construct(StoreMembershipService $store_membership_service)
    {
        $this->store_membership_service = $store_membership_service;
    }

    public function index(Request $request, $subdomain)
    {
        // Load active memberships (membership becomes inactive if cancelled/recurring subscription OR Expired/location_pass type OR all credits used/expired)
        // Data to show:
        // 1. Type location_pass
        //      one_time: time left to expiration/remaining passes (pass mode) + exp + location restrictions;
        //      recuring: time left to exp for paid period, upcoming date of next charge
        // 2. Type corporate
        //      one_time:redemption code+usage so far+redemptions left;
        //      recurring:not-yet-all-used redemption codes produced by each charge with usage for each, upcoming date of next charge
        // 3. Type class_lesson, service, hybrid
        //      one_time: "unlimited credits" with expiration date for unlimited OR list of unused credits with exp date;
        //      recurring: "unlimited credits" with expiration date for unlimited OR list of unused credits with exp date, upcoming date of next charge

        $user = auth()->user();

        $memberships = Membership::where('user_id', $user->id)
                        ->where('status', StateType::get('active'))
                        ->orderBy('created_at', 'desc')
                        ->withCount(['session_credits as sessions_active_credits_count' => function (Builder $query) {
                            $query->where('status', StateType::get('active'))
                                  ->whereNull('used_at')
                                  ->where(function (Builder $query) {
                                        $query->whereNull('expiry_at')
                                              ->orWhere('expiry_at', '>', now());
                                });
                        }])
                        ->with(['membership_charges'])
                        ->get();

        return Inertia::render('Store/Member/Memberships/Index', [
            'memberships' => $memberships,
            'locations' => Location::active()->select('id', 'title')->get(),
            'classtypes' => ClassType::orderBy('id', 'desc')->select('title', 'id')->get(),
            'servicetypes' => ServiceType::orderBy('id', 'desc')->select('title', 'id')->get(),
            'pack_types' => PackType::labels(),
            // 'price_types' => StripePriceType::labels(),
            'page_title' => __('My memberships'),
            'header' => __('My memberships'),
        ]);
    }

    public function cancel(Request $request, $subdomain, Membership $membership)
    {
        $user = auth()->user();
        if(!$membership){
            $msg = __('Membership not found.');
            return $this->redirectBackError($msg);
        }
        if($membership->user_id != $user->id){
            $msg = __('Unauthorized access.');
            return $this->redirectBackError($msg);
        }

        //beta flow, quick check
        //to do: allow to cancel even if term is not served yet.
        $can_cancel = $this->store_membership_service->canCancel($membership);
        if($can_cancel == false){
            $msg = __('Subscription can not be cancelled at this time. ');
            $msg .= __('Minimum commitment term is ');
            $msg .= $membership->min_term.' '.Str::plural($membership->interval, $membership->min_term);
            return $this->redirectBackError($msg);
        }

        $msg = 'Cancellation will be supported soon';
        //TODO 
        // call $this->store_membership_service => call stripe, attempt cancel => update membership set cancel_at => show message
        return $this->redirectBackSuccess($msg);

    }
}
