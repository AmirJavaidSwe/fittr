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
        return [
            'name' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'start_date' => $this->faker->dateTime,
            'end_date' => $this->faker->dateTime,
            'instructor_id' => $this->faker->numberBetween(1, 10),
            'studio_id' => $this->faker->numberBetween(1, 10),
            'does_repeat' => $this->faker->boolean,
            'week_days' => $this->faker->randomElements(ClassLesson::WEEK_DAYS, $this->faker->numberBetween(1, 7)),
        ];
    }
}
