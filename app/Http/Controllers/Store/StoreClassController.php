<?php

namespace App\Http\Controllers\Store;

use App\Enums\ClassStatus;
use App\Http\Controllers\Controller;
use App\Models\Partner\ClassLesson;
use App\Models\Partner\ClassType;
use App\Models\Partner\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreClassController extends Controller
{
    public function index(Request $request)
    {

        $maxDaysTimetable = (session('business_seetings.days_max_timetable') ?? 30) - 1;

        $startDate = now(session('business_seetings.timezone'))->startOfDay();
        $endDate = $startDate->copy()->addDays($maxDaysTimetable)->endOfDay()->utc();
        $startDate = $startDate->utc();

        return Inertia::render('Store/Classes/Index', [
            'page_title' => __('Classes'),
            'header' => __('Classes'),
            'classes' => ClassLesson::active()
                ->with(['studio.location.images', 'studio.class_type_studios', 'instructor', 'classType', 'waitlists', 'bookings' => function (Builder $query) {
                    $query->active();
                }])
                ->when(count($request->class_type ?? []), function($query) use($request) {
                    $query->whereIn('class_type_id', $request->class_type);
                })
                ->when(count($request->instructor ?? []), function($query) use($request) {
                    $query->whereIn('instructor_id', $request->instructor); // Requires adjustments as per new changes of multiple class instructors.
                })
                // Left commented for discussion
                // ->when($request->time, function($query) use($request, $startDate) {

                //     $app_tz = now(config('app.timezone'))->timezone->toOffsetName();
                //     $user_tz = now(session('business_seetings.timezone'))->timezone->toOffsetName();
                //     // dd($app_tz, $user_tz);

                //     $raw = "DATE_FORMAT(CONVERT_TZ(start_date,'$app_tz','$user_tz'), '%p') = '".strtoupper($request->time)."'";
                //     $query->whereRaw($raw);
                //     /* $start = $startDate->copy();
                //     if($request->time == 'pm') {
                //         $start->addHours(12);
                //     }

                //     $end = $start->copy()->addHours(12);

                //     $query->where('start_date', '>=', $start)
                //     ->where('end_date', '<=', $end); */
                // })
                ->where('start_date', '>=', $startDate)
                ->where('end_date', '<=', $endDate)
                ->orderBy('start_date', 'asc')
                ->get()
                ->groupBy(function($item) {
                    return $item->start_date
                    ->tz(session('business_seetings.timezone'))
                    ->format('Y-m-d');
                }),
            'class_types' => ClassType::select('id as value', 'title as label')->get(),
            'instructors' => User::select('id as value', 'name as label')->instructor()->get(),
        ]);
    }

    public function show(Request $request, $subdomain, ClassLesson $class)
    {
        $loadRelations = ['studio.location.images', 'instructor', 'classType'];

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
