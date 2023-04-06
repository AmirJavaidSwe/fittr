<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\ClassLesson;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClassLesson::factory()->count(20)->create();
    }
}
