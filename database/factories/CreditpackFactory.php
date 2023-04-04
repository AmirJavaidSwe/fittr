<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Creditpack>
 */
class CreditpackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'line1' => $this->faker->sentence,
            'credits' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 10, 30),
        ];
    }
}
