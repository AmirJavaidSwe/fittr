<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CountrySeeder::class,
            TimezoneSeeder::class,
            FormatSeeder::class,
            UserSeeder::class,
            PackageSeeder::class,
            // InstanceSeeder::class,

            // ! This seeder runs a chain 1-4 and should remain commented out. Run 'php artisan db:seed --class=PartnerDatabaseSeeder' from console when needed
            // 1. Each user with role 'partner' will be assigned database credentials DB: fittr@users
            // 2. New database/db user will be created for each partner (existin will be dropped)
            // 3. Migrations will be ran on each partner database created
            // 4. Only first partner will have seeders run
            // PartnerDatabaseSeeder::class,

        ]);
    }
}
