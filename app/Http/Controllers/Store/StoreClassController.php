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

        return Inertia::render('Store/Classes/Index', [
            'page_title' => __('Classes'),
            'header' => __('Classes'),
            'classes' => ClassLesson::active()->public()
                ->with(['studio.location.images', 'studio.class_type_studios', 'instructor', 'classType', 'waitlists', 'bookings' => function (Builder $query) {
                    $query->active();
                }])
                ->when(count($request->class_type ?? []), function($query) use($request) {
                    $query->whereIn('class_type_id', $request->class_type);
                })
                ->when(count($request->instructor ?? []), function($query) use($request) {
                    $query->whereHas('instructor', fn($query) => $query->whereIn('instructor_id', $request->instructor));
                })
                // Left commented for discussion
                // ->when($request->time, function($query) use($request, $startDate) {

                //     $app_tz = now(config('app.timezone'))->timezone->toOffsetName();
                //     $user_tz = now(session('business_settings.timezone'))->timezone->toOffsetName();
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
                    ->tz(session('business_settings.timezone'))
                    ->format('Y-m-d');
                }),
            'class_types' => ClassType::select('id as value', 'title as label')->get(),
            'instructors' => User::select('id as value', 'name as label')->instructor()->get(),
            'waivers' => auth()->user() ? $waivers : [],
            'signed_waiver_ids' => auth()->user() ? UserWaiver::where('user_id', auth()?->user()?->id)->whereIn('waiver_id', $waivers->pluck('id'))->pluck('waiver_id')->toArray() : [],
            'user_waiver_ids' => auth()->user() ? UserWaiver::where('user_id', auth()?->user()?->id)->whereIn('waiver_id', $waivers->pluck('id'))->pluck('id')->toArray() : [],
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
