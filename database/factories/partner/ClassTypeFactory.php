<?php

namespace Database\Factories\Partner;

use App\Enums\StateType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ClassTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'status' => $this->faker->randomElement(StateType::all()),
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}