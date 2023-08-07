<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\ClassLesson;
use Illuminate\Database\Seeder;
use App\Models\Partner\User;

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

        $classes = ClassLesson::get();
        foreach($classes as $k => $v) {
            $instructors = User::instructor()->inRandomOrder()->take(2)->pluck('id')->toArray();
            $v->instructor()->sync($instructors);
        }
    }
}
