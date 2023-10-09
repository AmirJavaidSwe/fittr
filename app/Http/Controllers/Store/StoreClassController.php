<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Models\Partner\UserWaiver;
use App\Models\Partner\ClassLesson;
use App\Http\Controllers\Controller;
use App\Services\Store\StoreClassService;

class StoreClassController extends Controller
{
    public function __construct(StoreClassService $store_class_service)
    {
        $this->store_class_service = $store_class_service;
    }

    public function index(Request $request)
    {
        $classes = $this->store_class_service->timetableClasses();

        /* 4 filters */
        $studios = $classes->pluck('studio', 'studio_id');
        $locations = $studios->pluck('location', 'location_id')->sortBy('title');
        $class_types = $classes->pluck('classType', 'class_type_id')->sortBy('title');
        $instructors = $classes->pluck('instructors')->flatten()->sortBy('first_name')->keyBy('id');

        //TODO check waivers, possible duplication! 1 Query can be enough. Refactor vue also
        if(auth()->user()){
            $waivers = Waiver::where('show_at', 'checkout')->where('is_active', 1)->get();
            $user_waivers = UserWaiver::where('user_id', auth()?->user()?->id)->whereIn('waiver_id', $waivers->pluck('id'))->get();
            $signed_waiver_ids = $user_waivers->pluck('waiver_id')->toArray();
            $user_waiver_ids = $user_waivers->pluck('id')->toArray();
        }

        $data = [
            'page_title' => __('Classes'),
            'header' => __('Classes'),
            'classes' => $classes->groupBy(function($item) {
                return $item->start_date->tz(session('business_settings.timezone'))->format('Y-m-d');
            }),
            'locations' => $locations->values(),
            'class_types' => $class_types->values(),
            'instructors' => $instructors->values(),
            'waivers' => $waivers ?? [],
            'signed_waiver_ids' => $signed_waiver_ids ?? [],
            'user_waiver_ids' => $user_waiver_ids ?? [],
        ];

        return Inertia::render('Store/Classes/Index', $data);
    }

    public function show(Request $request, $subdomain, ClassLesson $class)
    {
        $loadRelations = ['studio.location.images', 'instructors', 'classType'];

        if(auth()->user()) {
            $loadRelations['bookings'] = function($query) {
                $query->active()->where('user_id', auth()->user()->id);
            };
        }

        return Inertia::render('Store/Classes/Show', [
            'page_title' => __('Classes'),
            'header' => __('Classes'),
            'classDetail' => $class->load($loadRelations),
        ]);
    }
}
