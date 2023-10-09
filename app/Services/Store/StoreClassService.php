<?php

namespace App\Services\Store;

use App\Models\Partner\ClassLesson;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StoreClassService
{
    public function timetableClasses(): Collection
    {
        $maxDaysTimetable = (session('business_settings.days_max_timetable') ?? 30) - 1;
        $startDate = now(session('business_settings.timezone'))->startOfDay();
        $endDate = $startDate->copy()->addDays($maxDaysTimetable)->endOfDay()->utc();
        $startDate = $startDate->utc();

        $classes = ClassLesson::active()
            ->public()
            ->with([
                'studio' => [
                    'location',
                    'class_type_studios',
                ],
                'instructors.profile.images',
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

        return $classes;
    }

}