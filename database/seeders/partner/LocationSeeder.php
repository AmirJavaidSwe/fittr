<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Location::factory()
            ->count(5)
            ->sequence(
                ['title' => 'Wandsworth'],
                ['title' => 'Borough'],
                ['title' => 'Richmond'],
                ['title' => 'Moorgate'],
                ['title' => 'Bank'],
                ['title' => 'Covent Garden'],
            )
            ->create();
    }
}
