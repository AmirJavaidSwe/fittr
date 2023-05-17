<?php

namespace App\Http\Controllers\Partner;

use Inertia\Inertia;
use App\Enums\AppUserSource;
use App\Models\Partner\User;
use Illuminate\Http\Request;
use App\Models\Partner\Location;
use App\Models\Partner\ClassLesson;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class PartnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Gate::denies('viewAny-'.AppUserSource::partner->name . '-dashboard-viewAny')) {
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
}
