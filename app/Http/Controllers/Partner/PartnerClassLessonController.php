<?php

namespace App\Http\Controllers\Partner;

// use App\Http\Requests\ImportFile;
use App\Enums\ClassStatus;
use App\Events\ClassParticipant;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Role;
use App\Models\SystemModule;
use App\Models\User;
use App\Models\Partner\Amenity;
use App\Models\Partner\ClassLesson;
use App\Models\Partner\ClassType;
use App\Models\Partner\Instructor;
use App\Models\Partner\Location;
use App\Models\Partner\Studio;
use App\Http\Requests\Partner\ClassFormRequest;
use App\Models\Partner\NotificationTemplate;
use App\Rules\ArrayFieldExistsInDatabase;
use App\Rules\UserPasswordMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
    public $type;
    public $runFilter;
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
        $this->order_dir = $request->query('order_dir', 'asc');
        $this->runFilter = $request->input('runFilter');

        $classes = ClassLesson::with([
            'studio.class_type_studios',
            'studio.location',
            'classType',
            'instructors',
            'waitlists',
            'bookings' => fn ($query) => $query->active()
        ])
            ->orderBy($this->order_by, $this->order_dir)
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->orWhere('id', intval($this->search))
                        ->orWhere('title', 'LIKE', '%' . $this->search . '%');
                });
            })
            // if applied filters
            ->when($this->runFilter == true, function ($query) use ($request) {
                // both starta and end date
                if (($request->has('start_date') && $request->start_date != null) || ($request->has('end_date') && $request->end_date != null)) {
                    if (($request->has('start_date') && $request->start_date != null) && ($request->has('end_date') && $request->end_date != null)) {
                        $start_date = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
                        $end_date = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');
                        $query->where(function ($query) use ($request, $start_date, $end_date) {
                            $query->whereBetween(DB::raw('CONCAT(start_date, " ", end_date)'), [$start_date, $end_date]);
                        });
                    }

                    // only start date
                    else if ($request->has('start_date') && $request->start_date != null) {
                        $start_date = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
                        $query->where(function ($query) use ($request, $start_date) {
                            $query->where('start_date', '>=', $start_date);
                        });
                    }

                    // only end date
                    else if ($request->has('end_date') && $request->end_date != null) {
                        $end_date = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');
                        $query->where(function ($query) use ($request, $end_date) {
                            $query->where('end_date', '<=', $end_date);
                        });
                    }
                }

                // apply instructors filters
                if ($request->has('instructor_id') && count($request->instructor_id)) {
                    $query->where(function ($query) use ($request) {
                        $query->whereHas('instructors', function ($q) {
                            $q->whereIn('id', request()->instructor_id);
                        });
                    });
                }

                // apply class_type filters
                if ($request->has('class_type_id') && count($request->class_type_id)) {
                    $query->where(function ($query) use ($request) {
                        $query->whereIn('class_type_id', $request->class_type_id);
                    });
                }

                // apply studio_id filters
                if ($request->has('studio_id') && count($request->studio_id)) {
                    $query->where(function ($query) use ($request) {
                        $query->whereIn('studio_id', $request->studio_id);
                    });
                }
                /// apply location_id filters
                if ($request->has('location_id') && count($request->location_id)) {
                    $query->where(function ($query) use ($request) {
                        $query->whereIn('studio_id', function ($subQuery) use ($request) {
                            $subQuery->select('id')
                            ->from('studios')
                            ->whereIn('location_id', $request->location_id);
                        });
                    });
                }
                // apply off_peak filter
                if ($request->has('is_off_peak')) {
                    $is_off_peak =  $request->is_off_peak;
                    if (is_numeric($is_off_peak)) {
                        $query->where(function ($query) use ($request, $is_off_peak) {
                            $query->where('is_off_peak', $is_off_peak);
                        });
                    }
                }

                // apply off_peak filter
                if ($request->has('status')) {
                    $query->where(function ($query) use ($request) {
                        $query->whereIn('status', $request->status);
                    });
                }
            })
            ->paginate($this->per_page)
            ->withQueryString();

        return Inertia::render('Partner/Class/Index', [
            'page_title' => __('Classes'),
            'header' => __('Classes'),
            'classes' => $classes,
            'statuses' => ClassStatus::labels(),
            'instructors' => Instructor::select('id', 'first_name', 'last_name', 'email', 'profile_photo_path')->get(),
            'classtypes' => ClassType::pluck('title', 'id'),
            'studios' => Studio::with(['class_type_studios', 'location'])
                ->select('id', 'title', 'location_id')
                ->orderBy('title', 'asc')
                ->get()
                ->map(function ($item) {
                    $item->title = $item->location?->title . ' / ' . $item->title;
                    return $item;
                }),
            'roles' => Role::select('id', 'title')->where('source', auth()->user()->source)->where('business_id', auth()->user()->business_id)->get(),
            'users' => User::select('id', 'first_name', 'last_name', 'email')->partner()->where('business_id', auth()->user()->business_id)->get(),
            'locations' => Location::pluck('title', 'id'),
            'countries' => Country::select('id', 'name')->whereStatus(1)->get(),
            'amenities' => Amenity::select('id', 'title')->get()->map(fn ($item) => ['label' => $item->title, 'value' => $item->id]),
            'systemModules' => SystemModule::with('permissions')->where('is_for', auth()->user()->source)->get(),
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
            'instructors' => Instructor::select('id', 'first_name', 'last_name', 'email', 'profile_photo_path')->get(),
            'classtypes' => ClassType::latest('id')->pluck('title', 'id'),
            'locations' => Location::latest('id')->pluck('title', 'id'),
            'roles' => Role::latest('id')->pluck('title', 'id')->where('source', auth()->user()->source)->where('business_id', auth()->user()->business_id)->pluck('title', 'id'),
            'users' => User::select('id', 'first_name', 'last_name', 'email')->partner()->where('business_id', auth()->user()->business_id)->get(),
            'studios' => Studio::with(['class_type_studios', 'location'])
                ->select('id', 'title', 'location_id')
                ->orderBy('title', 'asc')
                ->get()
                ->map(function ($item) {
                    $item->title = $item->location?->title . ' / ' . $item->title;
                    return $item;
                }),
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
        $validated['end_date'] = Carbon::parse($validated['start_date'])->addMinutes($validated['duration']);

        //if class will be repeating itself, let's show preview page with a list/number of classes that will be created
        if ($request->does_repeat) {
            //parse dates:
            $start_date = Carbon::parse($validated['start_date']);
            $end_date = Carbon::parse($validated['end_date']);
            $repeat_end_date = Carbon::parse($validated['repeat_end_date']);
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

                    $class = ClassLesson::create($class_data);

                    $class->instructors()->sync($class_data['instructor_id'] ?? []);
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
                'instructors' => Instructor::whereIn('id', $request->instructor_id)->get(),
                'classtype' => ClassType::find($request->class_type_id),
                'studio' => Studio::find($request->studio_id),
                'class_duration' => $class_duration,
                'repeats' => $repeats,
                'repeats_count' => $period->count(),
            ]);
        }

        $class = ClassLesson::create($validated);

        $class->instructors()->sync($validated['instructor_id'] ?? []);

        return $this->redirectBackSuccess(__('Class updated successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param ClassLesson $class
     * @return Response
     */
    public function show(ClassLesson $class, Request $request)
    {
        $this->per_page = $request->query('per_page', 5);
        $this->type = $request->query('type', 'bookings');

        $bookings = $class->bookings()
            ->with('user')
            ->active()
            ->paginate($this->type == 'bookings' ? $this->per_page : 5);

        $waitlists = $class->waitlists()
            ->with('user')
            ->paginate($this->type == 'waitlists' ? $this->per_page : 5);

        $cancellations = $class->bookings()
            ->with('user')
            ->cancelled()
            ->paginate($this->type == 'bookings' ? $this->per_page : 5);

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
            'class_lesson' => $class->load(['studio.class_type_studios', 'classType', 'instructors', 'waitlists.user', 'bookings.user']),
            'bookings' => $bookings,
            'waitlists' => $waitlists,
            'cancellations' => $cancellations,
            'per_page' => intval($this->per_page),
            'type' => $this->type,
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
            'class_lesson' => $class->load(['studio', 'instructors', 'classType']),
            'statuses' => ClassStatus::labels(),
            'instructors' => Instructor::select('id', 'first_name', 'last_name', 'email', 'profile_photo_path')->get(),
            'classtypes' => ClassType::orderBy('id', 'desc')->pluck('title', 'id'),
            'studios' => Studio::with(['class_type_studios', 'location'])
                ->select('id', 'title', 'location_id')
                ->orderBy('title', 'asc')
                ->get()
                ->map(function ($item) {
                    $item->title = $item->location?->title . ' / ' . $item->title;
                    return $item;
                }),
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
        $validated = $request->validated();
        $validated['end_date'] = Carbon::parse($validated['start_date'])->addMinutes($validated['duration']);
        $class->fill($validated);
        $class->save();
        $class->instructors()->sync($validated['instructor_id'] ?? []);

        if ($request->does_repeat) {
            $start_date = $class->start_date->copy();
            $class_duration = $class->duration;
            $repeat_end_date = Carbon::parse($validated['repeat_end_date']);

            // $week_days: in ISO 8601: 0 => Mon, 1 => Tue, 2 => Wed, 3 => Thu, 4 => Fri, 5 => Sat, 6 => Sun
            // isoWeekday returns a number between 1 (monday) and 7 (sunday)
            $week_days = array_map(function ($v) {
                return $v + 1;
            }, $request->week_days);

            //create a period with 1 day interval
            $period = $start_date->toPeriod($repeat_end_date);

            //keep period dates that match selected weekdays but exclude donor class
            $period->filter(function (Carbon $date) use ($week_days) {
                return in_array($date->isoWeekday(), $week_days);
            });

            $repeats = array();
            foreach ($period as $date) {
                $repeats[] = $date;
            }

            //preview_confirmed comes from confirmation page and if so, we need to create bulk classes and redirect back to index
            if ($request->filled('preview_confirmed')) {
                //use existing class, create repeats
                $class_instructors = $class->instructor->pluck('id');
                foreach ($repeats as $start_date) {
                    $repeat_class = $class->replicate();
                    $repeat_class->start_date = $start_date;
                    $repeat_class->end_date = $start_date->copy()->addMinutes($class_duration);
                    $repeat_class->save();
                    $repeat_class->instructors()->sync($class_instructors);
                }

                return $this->redirectBackSuccess(__('Class updated and new classes created successfully'), 'partner.classes.index');
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
                'form_data' => array_merge($validated, ['preview_confirmed' => true, 'is_edit' => true, 'edit_class' => $class]),
                'instructors' => Instructor::whereIn('id', $request->instructor_id)->get(),
                'classtype' => ClassType::find($request->class_type_id),
                'studio' => Studio::find($request->studio_id),
                'class_duration' => $class_duration,
                'repeats' => $repeats,
                'repeats_count' => $period->count(),
            ]);
        }

        return $this->redirectBackSuccess(__('Class updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ClassLesson $class
     * @return RedirectResponse
     */
    public function destroy(ClassLesson $class)
    {
        // TODO: prevent delete here or early, when class has active bookings
        $class->instructors()->sync([]);
        $class->delete();

        // TODO: if bookings exist and admin really wants to delete a class, we need to cancel and refund all bookings made for this class

        return $this->redirectBackSuccess(__('Class deleted successfully'), 'partner.classes.index');
    }

    public function import(ImportFile $request)
    {
    }

    public function export()
    {
    }

    public function exportClasses()
    {/*
        $classes = ClassLesson::whereIn('id', request('classes'))->get();

        $export = SimpleExcelWriter::streamDownload('classes_' . date('d') . '_' . date('M') . '_' . date('Y') . '.csv');

        $export->addHeader(['Title', 'Start Date', 'End Date', 'Instructor', 'Studio ID', 'Status']);

        $i = 0;

        foreach ($classes as $class) {
            $i++;

            $export->addRow([
                $class->name,
                $class->start_date,
                $class->end_date,
                implode(', ', $class->instructors()->pluck('name')->toArray()),
                $class->studio()->first()->name,
                $class->status
            ]);

            if ($i % 1000 === 0) {
                flush(); // Flush the buffer every 1000 rows
            }
        }

        $export->toBrowser();
     */
    }

    public function bulkEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['nullable', new Enum(ClassStatus::class)],
            'instructor_id' => ['nullable', 'array', new ArrayFieldExistsInDatabase('mysql_partner', 'users', 'id')],
            'class_type_id' => 'nullable|integer|exists:mysql_partner.class_types,id',
            'studio_id' => 'nullable|integer|exists:mysql_partner.studios,id',
            'password' => ['required', new UserPasswordMatch],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return back()->withErrors($errors)->withInput();
        }

        $classes = ClassLesson::whereIn('id', request()->ids)->get();

        foreach ($classes as $k => $class) {
            if(request()->studio_id) {
                $class->studio_id = request()->studio_id;
            }
            if(request()->use_defaults == false) {
                $class->spaces = request()->spaces;
            }
            if(request()->class_type_id) {
                $class->class_type_id = request()->class_type_id;
            }
            if(request()->status) {
                $class->status = request()->status;
            }
            $class->save();
            if(!empty(request()->instructor_id)) {
                $class->instructors()->sync(request()->instructor_id);
            }
        }

        return $this->redirectBackSuccess(__('Classes updated successfully'), 'partner.classes.index');
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', new UserPasswordMatch],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return back()->withErrors($errors)->withInput();
        }

        $classes = ClassLesson::whereIn('id', request()->ids)->get();

        foreach ($classes as $k => $class) {
            $class->instructors()->detach();
            $class->delete();
        }

        return $this->redirectBackSuccess(__('Classes deleted successfully'), 'partner.classes.index');
    }

    public function bulkCopy(Request $request)
    {
        $classes = [];


        if ($request->start_date && $request->end_date && $request->shift_period && $request->shift_repeat) {
            $request->validate(
                [
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                    'shift_period' => 'required|numeric|min:1',
                    'shift_repeat' => 'required|numeric|min:1',
                    'class_type_id' => 'nullable|numeric|min:1',
                    'studio_id' => 'nullable|numeric|min:1',
                ],
                [],
                [
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'shift_period' => 'Shift Period (In Days)',
                    'shift_repeat' => 'Shift Repeat',
                    'class_type_id' => 'Class Type',
                    'studio_id' => 'Studio',
                ]
            );

            $timezone = session('business_settings.timezone');
            $start_date = Carbon::parse($request->start_date, $timezone)->startOfDay()->utc();
            $end_date = Carbon::parse($request->end_date, $timezone)->endOfDay()->utc();

            $classes = ClassLesson::with('classType', 'studio.location', 'instructors')
                ->where('start_date', '>=', $start_date)
                ->where('end_date', '<=', $end_date)
                ->when($request->class_type_id, fn ($query) => $query->where('class_type_id', $request->class_type_id))
                ->when($request->studio_id, fn ($query) => $query->where('studio_id', $request->studio_id))
                ->get()
                ->map(function ($item) use ($request) {
                    $item->shift_period = $request->shift_period;

                    if ($request->shift_repeat && $request->shift_repeat >= 1) {
                        $lastDatetime = $item->start_date;
                        $newDateTimes = [];

                        foreach (range(1, $request->shift_repeat) as $repeat) {
                            array_push(
                                $newDateTimes,
                                $lastDatetime->copy()
                                    ->addDays($request->shift_period * $repeat)
                            );
                        }
                        $item->new_date_time = $newDateTimes;
                    }

                    return $item;
                });
        }

        return Inertia::render('Partner/Class/BulkCopy', [
            'page_title' => __('Bulk Copy'),
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
                    'title' => __('Bulk Copy'),
                    'link' => null,
                ],
            ),
            'classtypes' => ClassType::pluck('title', 'id'),
            'studios' => Studio::with('class_type_studios')->select('id', 'title')->orderBy('title', 'asc')->get(),
            'classes' => $classes,
        ]);
    }

    public function storeBulkCopy(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'shift_period' => 'required|numeric|min:1',
                'shift_repeat' => 'required|numeric|min:1',
                'class_type_id' => 'nullable|numeric|min:1',
                'studio_id' => 'nullable|numeric|min:1',
                'ids' => 'required|array',
            ],
            [
                'ids.required' => __('Class selection is required'),
                'ids.array' => __('Invalid class selection.'),
            ],
            [
                'start_date' => 'Start Date',
                'end_date' => 'End Date',
                'shift_period' => 'Shift Period (In Days)',
                'shift_repeat' => 'Shift Repeat',
                'class_type_id' => 'Class Type',
                'studio_id' => 'Studio',
            ]
        );

        if ($validator->fails()) {
            $errorMsg = "";
            foreach ($validator->getMessageBag()->getMessages() as $message) {
                $message = $message[0];
                $errorMsg .= $message ? $message . "\n" : "";
            }

            return $this->redirectBackError($errorMsg);
        }

        $this->copied_classes_count = 0;
        ClassLesson::with(['instructors' => fn ($query) => $query->select('users.id')])
            ->whereIn('id', $request->ids)
            ->each(function ($class) use ($request) {
                foreach (range(1, $request->shift_repeat) as $repeat) {
                    $classModel = $class->replicate();
                    $classModel->start_date = $classModel->start_date->addDays($request->shift_period * $repeat);
                    $classModel->end_date = $classModel->end_date->addDays($request->shift_period * $repeat);
                    $classModel->save();
                    $classModel->instructors()->sync($class->instructors->pluck('id'));

                    $this->copied_classes_count++;
                }
            });

        $noun = Str::of('class')->plural($this->copied_classes_count);

        return $this->redirectBackSuccess(__(':count new :noun have been created.', ['count' => $this->copied_classes_count, 'noun' => $noun]), 'partner.classes.index');
    }

    public function participants(Request $request, ClassLesson $class)
    {
        $class->load(['bookings' => fn ($query) => $query->with('user')->active()]);
        $template = NotificationTemplate::where('key', 'CLASS_PARTICIPANTS')->first();

        return response()->json(['participants' => $class->bookings, 'template' => $template]);
    }

    public function emailClass(Request $request, ClassLesson $class)
    {
        $class->load([
            'classType', 'studio.location',
            'bookings' => function ($query) use ($request) {
                $query->with('user')
                    ->active()
                    ->whereHas('user', fn ($subQuery) => $subQuery->whereIn('id', $request->ids));
            },
        ]);

        event(new ClassParticipant($class, $request->all()));
        return $this->redirectBackSuccess('The email has been sent to the selected participants.');
    }
}
