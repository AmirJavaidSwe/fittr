<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\ClassType;
use Illuminate\Database\Seeder;

class ClasstypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClassType::factory()
            ->count(9)
            ->sequence(
                ['title' => 'cycle'],
                ['title' => 'circuits'],
                ['title' => 'yoga'],
                ['title' => 'barre'],
                ['title' => 'pilates'],
                ['title' => 'dance'],
                ['title' => 'hiit'],
                ['title' => 'wall climbing'],
                ['title' => 'water aerobics'],
            )
            ->create();
    }
}
