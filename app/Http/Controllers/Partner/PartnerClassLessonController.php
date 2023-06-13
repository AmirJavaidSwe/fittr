<?php

namespace App\Http\Controllers\Partner;

use App\Enums\ClassStatus;
use App\Http\Controllers\Controller;
// use App\Http\Requests\ImportFile;
use App\Http\Requests\Partner\ClassFormRequest;
use App\Models\Partner\ClassLesson;
use App\Models\Partner\ClassType;
use App\Models\Partner\Instructor;
use App\Models\Partner\Studio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

// use Spatie\SimpleExcel\SimpleExcelReader;
// use Spatie\SimpleExcel\SimpleExcelWriter;

class PartnerClassLessonController extends Controller
{

    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Partner/Class/Index', [
            'page_title' => __('Classes'),
            'header' => __('Classes'),
            // 'classes' => $query,
            'classes' => ClassLesson::with('studio', 'classType', 'instructor')->orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function ($query) {
                        $query->orWhere('id', intval($this->search))
                            ->orWhere('title', 'LIKE', '%' . $this->search . '%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'statuses' => ClassStatus::labels(),
            'instructors' => Instructor::orderBy('id', 'desc')->pluck('name', 'id'),
            'classtypes' => ClassType::orderBy('id', 'desc')->pluck('title', 'id'),
            'studios' => Studio::orderBy('id', 'desc')->pluck('title', 'id'),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return Inertia::render('Partner/Class/Create', [
            'page_title' => __('Create new Class'),
            'header' => array(
                [
                    'title' => __('Classes'),
                    'link' => route('partner.classes.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Create new Class'),
                    'link' => null,
                ],
            ),
            'statuses' => ClassStatus::labels(),
            'instructors' => Instructor::orderBy('id', 'desc')->pluck('name', 'id'),
            'classtypes' => ClassType::orderBy('id', 'desc')->pluck('title', 'id'),
            'studios' => Studio::orderBy('id', 'desc')->pluck('title', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ClassFormRequest $request
     * @return RedirectResponse
     */
    public function store(ClassFormRequest $request)
    {
        $validated = $request->validated();

        //if class will be repeating itself, let's show preview page with a list/number of classes that will be created
        if ($request->does_repeat) {
            //parse dates:
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $repeat_end_date = Carbon::parse($request->repeat_end_date);
            $class_duration = $start_date->diffInMinutes($end_date);

            // $week_days: in ISO 8601: 0 => Mon, 1 => Tue, 2 => Wed, 3 => Thu, 4 => Fri, 5 => Sat, 6 => Sun
            // isoWeekday returns a number between 1 (monday) and 7 (sunday)
            $week_days = array_map(function ($v) {
                return $v + 1;
            }, $request->week_days);

            //create a period with 1 day interval
            $period = $start_date->toPeriod($repeat_end_date);

            //keep period dates that match selected weekdays and keep initial donor class
            $period->filter(function (Carbon $date) use ($week_days, $start_date) {
                return $date->isSameDay($start_date) || in_array($date->isoWeekday(), $week_days);
            });

            $repeats = array();
            foreach ($period as $date) {
                $repeats[] = $date;
            }

            //preview_confirmed comes from confirmation page and if so, we need to create bulk classes and redirect back to index
            if ($request->filled('preview_confirmed')) {
                foreach ($repeats as $start_date) {
                    $class_data = $validated;
                    $class_data['start_date'] = $start_date;
                    $class_data['end_date'] = $start_date->copy()->addMinutes($class_duration);
                    ClassLesson::create($class_data);
                }
                return $this->redirectBackSuccess(__('Classes created successfully'), 'partner.classes.index');
            }

            return Inertia::render('Partner/Class/RepeatPreview', [
                'page_title' => __('Confirm classes'),
                'header' => array(
                    [
                        'title' => __('Classes'),
                        'link' => route('partner.classes.index'),
                    ],
                    [
                        'title' => '/',
                        'link' => null,
                    ],
                    [
                        'title' => __('Review and confirm'),
                        'link' => null,
                    ],
                ),
                'form_data' => array_merge($validated, ['preview_confirmed' => true]),
                'instructor' => Instructor::find($request->instructor_id),
                'classtype' => ClassType::find($request->class_type_id),
                'studio' => Studio::find($request->studio_id),
                'class_duration' => $class_duration,
                'repeats' => $repeats,
                'repeats_count' => $period->count(),
            ]);
        }

        ClassLesson::create($validated);

        return $this->redirectBackSuccess(__('Class updated successfully'), 'partner.classes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param ClassLesson $class
     * @return Response
     */
    public function show(ClassLesson $class)
    {
        return Inertia::render('Partner/Class/Show', [
            'page_title' => __('Class details'),
            'header' => array(
                [
                    'title' => __('Classes'),
                    'link' => route('partner.classes.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Class details'),
                    'link' => null,
                ],
            ),
            'class_lesson' => $class->load(['studio', 'instructor', 'classType']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ClassLesson $class
     * @return JsonResponse|Response
     */
    public function edit(ClassLesson $class)
    {
        return Inertia::render('Partner/Class/Edit', [
            'page_title' => __('Edit Class'),
            'header' => array(
                [
                    'title' => __('Classes'),
                    'link' => route('partner.classes.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit Class'),
                    'link' => null,
                ],
            ),
            'class_lesson' => $class,
            'statuses' => ClassStatus::labels(),
            'instructors' => Instructor::orderBy('id', 'desc')->pluck('name', 'id'),
            'classtypes' => ClassType::orderBy('id', 'desc')->pluck('title', 'id'),
            'studios' => Studio::orderBy('id', 'desc')->pluck('title', 'id'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ClassFormRequest $request
     * @param ClassLesson $class
     * @return RedirectResponse
     */
    public function update(ClassFormRequest $request, ClassLesson $class)
    {
        $class->update($request->validated());

        return $this->redirectBackSuccess(__('Class updated successfully'), 'partner.classes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ClassLesson $class
     * @return RedirectResponse
     */
    public function destroy(ClassLesson $class)
    {
        //TODO: prevent delete here or early, when class has active bookings
        $class->delete();

        //TODO: if bookings exist and admin really wants to delete a class, we need to cancel and refund all bookings made for this class

        return $this->redirectBackSuccess(__('Class deleted successfully'), 'partner.classes.index');
    }

    public function import(ImportFile $request)
    {

    }

    public function export()
    {

    }

    public function exportClasses()
    {
        $classes = ClassLesson::whereIn('id', request('classes'))->get();

        $export = SimpleExcelWriter::streamDownload('classes_' . date('d') . '_' . date('M') . '_' . date('Y') . '.csv');

        $export->addHeader(['Title', 'Start Date', 'End Date', 'Instructor ID', 'Studio ID', 'Status']);

        $i = 0;

        foreach ($classes as $class) {
            $i++;

            $export->addRow([
                $class->name,
                $class->start_date,
                $class->end_date,
                $class->instructor_id,
                $class->studio_id,
                $class->status
            ]);

            if ($i % 1000 === 0) {
                flush(); // Flush the buffer every 1000 rows
            }
        }

        $export->toBrowser();
    }

}