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
            'title' => $this->faker->sentence(3),
            'page_title' => $this->faker->sentence(3),
            'brief' => $this->faker->sentence(3),
            'address_line_1' => $this->faker->sentence(3),
            'address_line_2' => $this->faker->sentence(3),
            'city' => $this->faker->sentence(3),
            'county' => $this->faker->sentence(3),
            'postcode' => $this->faker->sentence(3),
            'tel' => $this->faker->sentence(3),
            'map_latitude' => $this->faker->sentence(3),
            'map_longitude' => $this->faker->sentence(3),
            'ordering' => rand(1, 100),
            'status' => rand(0, 1)
        ];
    }
}
