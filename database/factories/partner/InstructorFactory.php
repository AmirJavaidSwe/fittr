<?php

namespace Database\Factories\Partner;

use App\Enums\PartnerUserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instructor>
 */
class InstructorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'role' => PartnerUserRole::INSTRUCTOR->value,
            'email' => $this->faker->email
        ];
    }
}
