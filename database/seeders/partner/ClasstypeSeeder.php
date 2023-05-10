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
        ClassType::factory()->count(5)->create();
    }
}
