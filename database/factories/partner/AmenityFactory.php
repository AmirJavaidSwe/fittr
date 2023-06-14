<?php

namespace Database\Factories\Partner;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * @extends Factory<\App\Models\Amenity>
 */
class AmenityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = storage_path('app/public/images/amenity');
        $public_folder = 'images/amenity';
        Storage::disk('public')->makeDirectory($public_folder);

        $filename = $this->faker->image($path, 100, 100, null, false);

        return [
            'title' => $this->faker->word,
            'icon' => $filename,
            'contents' => $this->faker->paragraph,
            'ordering' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->numberBetween(0, 1)
        ];
    }
}
