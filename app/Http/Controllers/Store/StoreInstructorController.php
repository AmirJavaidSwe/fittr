<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Partner\Instructor;
use App\Http\Controllers\Controller;
use App\Services\Store\StoreClassService;

class StoreInstructorController extends Controller
{
    public $instructor;

    public function __construct(StoreClassService $store_class_service)
    {
        $this->store_class_service = $store_class_service;
    }

    public function index(Request $request)
    {
        $instructors = Instructor::with(['classTypes', 'profile.images'])->get();
        $class_types = $instructors->pluck('classTypes')->flatten()->keyBy('id')->sortBy('title')->prepend(['id' => 0, 'title' => __('ALL')])->values();

        return Inertia::render('Store/Instructors/InstructorsList', [
            'page_title' => __('Instructors'),
            'header' => __('Instructors'),
            'instructors' => $instructors,
            'class_types' => $class_types
        ]);
    }

    public function show(Request $request)
    {
        $this->instructor = Instructor::with(['classTypes', 'profile.images'])->findOrFail($request->id);
        $classes = $this->store_class_service->timetableClasses()
            ->filter(function ($class) {
                return $class->instructors->contains(function ($instructor) {
                    return $instructor->id == $this->instructor->id;
                });
            });

        return Inertia::render('Store/Instructors/InstructorProfile', [
            'instructor' => $this->instructor,
            'classes' => $classes->groupBy(function($item) {
                return $item->start_date->tz(session('business_settings.timezone'))->format('Y-m-d');
            }),
        ]);
    }
}
