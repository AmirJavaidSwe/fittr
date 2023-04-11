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
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->cityPrefix(),
            'city' => $this->faker->city(),
            'county' => $this->faker->sentence(3),
            'postcode' => $this->faker->postcode(),
            'tel' => $this->faker->phoneNumber(),
            'map_latitude' => $this->faker->latitude(),
            'map_longitude' => $this->faker->longitude(),
            'ordering' => rand(1, 100),
            'status' => rand(0, 1)
        ];
    }
}
