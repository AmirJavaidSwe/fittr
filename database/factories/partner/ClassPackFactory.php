<?php

namespace Database\Factories\Partner;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassPack>
 */
class ClasspackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sessions' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 10, 30),
        ];
    }
}
