<?php

namespace App\Observers;

use App\Models\Partner\ClassLesson;

class ClassLessonObserver
{
    public function saving(ClassLesson $classLesson)
    {
        $this->updateOriginalInstructors($classLesson);
    }

    private function updateOriginalInstructors($classLesson) {

        $old_instructor_ids = $classLesson->instructor()->pluck('id')->toArray();
        $new_instructor_ids = request()->instructor_id;
        $diff = array_diff(array_values($old_instructor_ids), array_values($new_instructor_ids));
        if(count($diff)) {
            $instructors = $classLesson->instructor()->pluck('name', 'id')->toArray();
            $old_original_instructors = $classLesson->original_instructors;
            if(!is_array($old_original_instructors)) {
                $old_original_instructors = [];
            }
            $array = [];
            $array = $old_original_instructors;
            $array[] = $instructors;
            $classLesson->original_instructors = $array;
        }
    }
}
