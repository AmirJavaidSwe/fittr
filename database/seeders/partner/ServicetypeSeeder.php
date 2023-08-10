<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\ServiceType;
use Illuminate\Database\Seeder;

class ServicetypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ServiceType::factory()
            ->count(3)
            ->sequence(
                ['title' => 'body hydration'],
                ['title' => 'massage'],
                ['title' => 'personal trainer'],
            )
            ->create();
    }
}
