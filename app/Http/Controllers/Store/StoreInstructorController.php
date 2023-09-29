<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Partner\User;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Models\Partner\Instructor;
use App\Models\Partner\UserWaiver;
use App\Models\Partner\ClassLesson;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Database\Eloquent\Builder;

class StoreInstructorController extends Controller
{
    public function index(Request $request)
    {
        $instructors = User::instructor()->with('profile.images')->get()->each(function ($instructor) {
            $instructor->email = Str::mask($instructor->email, '*', 3);
        });


        return Inertia::render('Store/Instructor/InstructorsList', [
            'page_title' => __('Instructors'),
            'header' => __('Instructors'),
            'instructors' => $instructors,
        ]);
    }

    public function show(Request $request){
        $instructor = Instructor::find($request->id);
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
        return Inertia::render('Store/Instructor/InstructorProfile', [
            'page_title' => __('Instructor-Profile'),
            'header' => __('Instructors'),
            'instructor' => $instructor,
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

    public function showInstructorClass(Request $request){
        $instructor = Instructor::find($request->id);
    }
}
