<?php

namespace Database\Factories\Partner;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Locations>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'title' => 'Location '.$this->faker->randomLetter(),
            'page_title' => $this->faker->sentence(3),
            'brief' => $this->faker->sentence(3),
            'url' => $this->faker->url(),
            'checkin_url' => $this->faker->url(),
            'manager_id' => 1,
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->cityPrefix(),
            'city' => $this->faker->city(),
            'country_id' => 232,
            'postcode' => $this->faker->postcode(),
            'tel' => $this->faker->phoneNumber(),
            'map_latitude' => $this->faker->latitude(),
            'map_longitude' => $this->faker->longitude(),
            'email' => $this->faker->email(),
            'ordering' => rand(1, 100),
            'status' => rand(0, 1)
        ];
    }
}
