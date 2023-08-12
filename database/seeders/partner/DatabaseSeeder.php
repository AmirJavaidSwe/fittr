<?php

namespace Database\Seeders\Partner;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the partner's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            AmenitySeeder::class,
            ClasstypeSeeder::class,
            ServicetypeSeeder::class,
            ClassesSeeder::class,
            LocationSeeder::class,
            StudioSeeder::class,
            PackSeeder::class,
        ]);
    }
}
