<?php

namespace Database\Factories\Partner;

use App\Models\Partner\ClassLesson;
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

        return [
            'name' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'is_offpeak' => $this->faker->boolean,
            'start_date' => $dt,
            'end_date' => $dt->copy()->addMinutes(45),
            'instructor_id' => $this->faker->numberBetween(1, 5),
            'studio_id' => $this->faker->numberBetween(1, 5),
        ];
    }
}
