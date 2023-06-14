<?php

namespace Database\Factories\Partner;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class StudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => 'Studio '.$this->faker->randomLetter(),
            'page_title' => $this->faker->sentence(3),
            'brief' => $this->faker->sentence(3),
            'ordering' => rand(1, 100),
            'status' => rand(0, 1),
            'location_id' => rand(1, 5)
        ];
    }
}
