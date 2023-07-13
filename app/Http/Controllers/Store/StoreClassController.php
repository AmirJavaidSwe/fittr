<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\ClassLesson;
use App\Models\Partner\ClassType;
use App\Models\Partner\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreClassController extends Controller
{
    public function index(Request $request)
    {
        //Needs work with dates and tz - wrong results on edges!
        $searchDate = $request->get('date', now(session('business_seetings.timezone')));

        $eagerLoad = ['studio.location.images', 'instructor', 'classType'];

        if(auth()->user()) {
            $eagerLoad['bookings'] = function($query) {
                $query->active()->where('user_id', auth()->user()->id);
            };
        }

        return Inertia::render('Store/Classes/Index', [
            'page_title' => __('Classes'),
            'header' => __('Classes'),
            'classes' => ClassLesson::active()
                ->with($eagerLoad)
                ->when($request->class_type, function($query) use($request) {
                    $query->where('class_type_id', $request->class_type);
                })
                ->when($request->instructor, function($query) use($request) {
                    $query->where('instructor_id', $request->instructor);
                })
                ->when($request->time, function($query) use($request, $searchDate) {
                    $start = Carbon::parse($searchDate, session('business_seetings.timezone'));
                    if($request->time == 'pm') {
                        $start->addHours(12);
                    }

                    $end = $start->clone()->addHours(12);

                    $query->where('start_date', '>', $start->utc())
                    ->where('end_date', '<', $end->utc());
                })
                ->when(!$request->time, function($query) use($searchDate) {
                    $query->whereDate('start_date', $searchDate);
                })
                ->orderBy('start_date', 'asc')
                ->get(),
            'class_types' => ClassType::select('id as value', 'title as label')->get(),
            'instructors' => User::select('id as value', 'name as label')->instructor()->get(),
        ]);
    }
}
