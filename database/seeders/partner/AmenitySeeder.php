<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Storage::disk('public')->deleteDirectory('images/amenity');

        return Amenity::factory()->count(5)->create();
    }
}
