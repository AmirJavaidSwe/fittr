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
        dump('seeding classes');

        ClassLesson::factory()->count(20)->create();

        $classes = ClassLesson::get();
        foreach($classes as $class) {
            $instructors = User::instructor()->inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $class->instructors()->sync($instructors);
            $class->update([
                'title' => ucfirst($class->classType->title).' '.$class->duration,
            ]);
        }
    }
}
