<?php

namespace Database\Factories\Partner;

use App\Models\Partner\ClassLesson;
use App\Models\Partner\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassLesson>
 */
class ClassLessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dt = today()->addDays(rand(1, 10))->addHour(rand(6, 10));

        $instructors = User::instructor()->get();

        return [
            'title' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'is_off_peak' => $this->faker->boolean,
            'start_date' => $dt,
            'end_date' => $dt->copy()->addMinutes(45),
            'class_type_id' => $this->faker->numberBetween(1, 10),
            'studio_id' => $this->faker->numberBetween(1, 7),
            'spaces' => $this->faker->numberBetween(1, 50),
        ];
    }
}
