<?php

namespace App\Http\Controllers\Partner;

use App\Models\Role;
use Inertia\Inertia;
use App\Models\Country;
use App\Enums\ClassStatus;
use App\Models\User;
use App\Models\SystemModule;
use Illuminate\Http\Request;
use App\Models\Partner\Studio;
use App\Models\Partner\Amenity;
use App\Models\Partner\Location;
use App\Models\Partner\ClassType;
use App\Models\Partner\Instructor;
use App\Http\Controllers\Controller;

class PartnerOnTheFlyResource extends Controller
{

    public function index()
    {
        $statuses = ClassStatus::labels();
        $instructors = Instructor::pluck('name', 'id');
        $classtypes = ClassType::pluck('title', 'id');
        $studios = Studio::latest('id')->pluck('title', 'id');
        $roles = Role::select('id', 'title')->where('source', auth()->user()->source)->where('business_id', auth()->user()->business_id)->get();
        $users = User::select('id', 'name', 'email')->partner()->where('business_id', auth()->user()->business_id)->get();
        $locations = Location::select('id', 'title')->get();
        $countries = Country::select('id', 'name')->whereStatus(1)->get();
        $amenities = Amenity::select('id', 'title')->get()->map(fn ($item) => ['label' => $item->title, 'value' => $item->id]);
        $systemModules = SystemModule::with('permissions')->where('is_for', auth()->user()->source)->get();

        return response()->json([
            'statuses' => $statuses,
            'instructors' => $instructors,
            'classtypes' => $classtypes,
            'studios' => $studios,
            'roles' => $roles,
            'users' => $users,
            'locations' => $locations,
            'countries' => $countries,
            'amenities' => $amenities,
            'systemModules' => $systemModules,
        ]);
    }
}
