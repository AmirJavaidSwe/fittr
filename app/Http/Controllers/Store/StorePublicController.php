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
        $totalClasses = ClassLesson::count();
        $totalInstructors = User::instructor()->count();
        $totalLocations = Location::count();

        return Inertia::render('Store/Homepage', [
            'page_title' => __('Homepage'),
            'header' => __('Homepage'),
            'totalClasses' => $totalClasses,
            'totalInstructors' => $totalInstructors,
            'totalLocations' => $totalLocations,
        ]);
    }
}
