<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\UserProfileController;
use App\Enums\AppUserSource;
use App\Models\Partner\User;
use App\Models\Partner\Location;
use App\Models\Partner\ClassLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PartnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Gate::denies('viewAny-'.AppUserSource::get('partner') . '-dashboard-viewAny')) {
            abort(403);
        }

        $partner = $request->user();

        $totalClasses = ClassLesson::count();
        $totalInstructors = User::instructor()->count();
        $totalMembers = User::member()->count();
        $totalLocations = Location::count();

        return Inertia::render('Partner/Dashboard', [
            'page_title' => __('Admin dashboard'),
            'header' => __('Admin dashboard'),
            'partner' => $partner,
            'totalClasses' => $totalClasses,
            'totalInstructors' => $totalInstructors,
            'totalMembers' => $totalMembers,
            'totalLocations' => $totalLocations,
        ]);
    }

    public function loginAs(Request $request)
    {
        $user = User::findOrFail($request->id);
        $controller = app()->make(UserProfileController::class);
        $bs = $this->business_settings();
        $subdomain = $bs['subdomain'] ?? null;
        if(empty($subdomain)){
            return $this->redirectBackError(__('Service store domain is not set'));
        }
        $url = $controller->redirectToSubdomain($user, $subdomain);

        return Inertia::location($url);
    }
}
