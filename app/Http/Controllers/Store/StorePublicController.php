<?php

namespace App\Http\Controllers\Store;

use App\Enums\StateType;
use App\Http\Controllers\Controller;
use App\Models\Partner\ClassLesson;
use App\Models\Partner\Location;
use App\Models\Partner\User;
use App\Models\Business;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StorePublicController extends Controller
{
    public function index(Request $request)
    {
        // TODO: add public business runtime config to be shared across all store
        // see App\Services\Partner\BusinessSettingService -> getByGroup(SettingGroup::service_store_general)
        $business_subdomain = config('business_subdomain');
        $business = Business::where('status', StateType::ACTIVE->value)->findOrFail($business_subdomain->business_id);
 
        $totalClasses = ClassLesson::count();
        $totalInstructors = User::instructor()->count();
        $totalLocations = Location::count();

        return Inertia::render('Store/Homepage', [
            'page_title' => __('Homepage'),
            'header' => __('Homepage'),
            'subdomain' => $business_subdomain,
            'totalClasses' => $totalClasses,
            'totalInstructors' => $totalInstructors,
            'totalLocations' => $totalLocations,
        ]);
    }
}
