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
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Database\Eloquent\Builder;

class StoreMembershipController extends Controller
{
    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;

    public function index(Request $request, $subdomain)
    {
        // Load active memberships (membership becomes inactive if cancelled/recurring subscription OR Expired/default type OR all credits used/expired)
        // Data to show:
        // 1. Type default
        //      one_time: time left to expiration;
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

    public function all(Request $request, $subdomain)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'created_at');
        $this->order_dir = $request->query('order_dir', 'desc');
        $user = auth()->user();
        
        return Inertia::render('Store/Member/Memberships/Table', [
            'memberships' => Membership::with([
                'pack',
                'pack_price',
                'order',
                'order_item',
                'user',
            ])
                ->orderBy($this->order_by, $this->order_dir)
                ->where('user_id', $user->id)
                ->paginate($this->per_page)
                ->withQueryString(),
            'pack_types' => PackType::labels(),
            'price_types' => StripePriceType::labels(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('My memberships'),
            'header' => __('My memberships'),
        ]);
    }
}
