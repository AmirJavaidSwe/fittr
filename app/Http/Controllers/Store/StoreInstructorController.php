<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Partner\User;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Models\Partner\ClassType;
use App\Models\Partner\Instructor;
use App\Models\Partner\UserWaiver;
use App\Models\Partner\ClassLesson;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Http\Controllers\Store\StoreClassController;

class StoreInstructorController extends Controller
{
    public function index(Request $request)
    {
        $instructors = User::instructor()->with('profile.images')->get()->each(function ($instructor, $index) {
            $instructor->email = Str::mask($instructor->email, '*', 3);
        });
        $instructors = $instructors->map(function ($instructor) {
            return $this->loadInstructorClasses($instructor);
        });

        $class_types = ClassType::select('title', 'id')->get()->map(function ($class_type){

            $class_type->title = ucwords($class_type->title);
            return $class_type;
        });

        return Inertia::render('Store/Instructor/InstructorsList', [
            'page_title' => __('Instructors'),
            'header' => __('Instructors'),
            'instructors' => $instructors,
            'class_types' => $class_types
        ]);
    }

    public function show(Request $request)
    {
        $instructor = Instructor::with('profile.images')->findOrFail($request->id);

        $instructor = $this->loadInstructorClasses($instructor);

        $request->merge([
            'instructor_id' => $request->id,
            'incoming_request_from_instructor_profile_page' => true,
        ]);

        $classes = new StoreClassController;

        $data = $classes->index($request);
        $data['page_title'] = __('Instructor-Profile');
        $data['header'] = __('Instructors');

        $data['instructor'] = $instructor;
        return Inertia::render('Store/Instructor/InstructorProfile', $data);
    }

    public function showInstructorClass(Request $request)
    {
        $instructor = Instructor::find($request->id);
    }

    private function loadInstructorClasses($instructor)
    {
        $instructor->with(['classes:id,class_type_id']); // Load only 'class_id' and 'class_type_id' columns from the 'classes' table

        $classType = $instructor->classes->filter(function ($class) {
            return $class->class_type_id; // Check for the existence of classType_id
        })->pluck('class_type_id');

        $titles = $classType->map(function ($classType_id) {
            // You can retrieve the title using a separate query if needed
            $title = ClassType::find($classType_id)->title;
            return ucwords($title);
        });

        $instructor->class_types = array_values(array_unique($titles->toArray()));

        // Unset the loaded relationship to avoid it being included in toArray, toJson, etc.
        $instructor->unsetRelation('classes');

        $instructor->email = Str::mask($instructor->email, '*', 3);

        return $instructor;
    }
}
