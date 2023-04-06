<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\Studio;
use Illuminate\Database\Seeder;

class StudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Studio::factory()->count(5)->create();
    }
}
