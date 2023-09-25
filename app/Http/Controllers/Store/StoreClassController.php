<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use App\Enums\ClassStatus;
use App\Models\Partner\User;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Models\Partner\ClassType;
use App\Models\Partner\UserWaiver;
use App\Models\Partner\ClassLesson;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Database\Eloquent\Builder;

class StoreClassController extends Controller
{
    public function index(Request $request)
    {

        $maxDaysTimetable = (session('business_settings.days_max_timetable') ?? 30) - 1;

        $startDate = now(session('business_settings.timezone'))->startOfDay();
        $endDate = $startDate->copy()->addDays($maxDaysTimetable)->endOfDay()->utc();
        $startDate = $startDate->utc();

        $waivers = Waiver::where('show_at', 'checkout')->where('is_active', 1)->get();
        $classes = ClassLesson::active()
            ->public()
            ->with([
                'studio' => [
                    'location',
                    'class_type_studios',
                ],
                'instructors',
                'classType',
                'waitlists',
                'bookings' => function (Builder $query) {
                    $query->active();
                }
            ])
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->orderBy('start_date', 'asc')
            ->get();

        $studios = $classes->pluck('studio', 'studio_id');
        $locations = $studios->pluck('location', 'location_id')->sortBy('title');
        $class_types = $classes->pluck('classType', 'class_type_id')->sortBy('title');
        $instructors = $classes->pluck('instructors')->flatten()->sortBy('first_name')->keyBy('id');

        return Inertia::render('Store/Classes/Index', [
            'page_title' => __('Classes'),
            'header' => __('Classes'),
            'classes' => $classes->groupBy(function($item) {
                    return $item->start_date
                    ->tz(session('business_settings.timezone'))
                    ->format('Y-m-d');
                }),
            'locations' => $locations->values(),
            // 'studios' => $studios->values(), not needed
            'class_types' => $class_types->values(),
            'instructors' => $instructors->values(),
            'waivers' => auth()->user() ? $waivers : [],
            'signed_waiver_ids' => auth()->user() ? UserWaiver::where('user_id', auth()?->user()?->id)->whereIn('waiver_id', $waivers->pluck('id'))->pluck('waiver_id')->toArray() : [],
            'user_waiver_ids' => auth()->user() ? UserWaiver::where('user_id', auth()?->user()?->id)->whereIn('waiver_id', $waivers->pluck('id'))->pluck('id')->toArray() : [],
        ]);
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
