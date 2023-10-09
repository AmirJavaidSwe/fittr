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
            ClasstypeSeeder::class,
            UserSeeder::class,
            AmenitySeeder::class,
            ServicetypeSeeder::class,
            LocationSeeder::class, //Studios added via Location
            ClassesSeeder::class,
            PackSeeder::class,
            RoleSeeder::class,
            NotificationTemplateSeeder::class,
        ]);
    }
}
