<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\ClassLesson;
use App\Models\Partner\ClassType;
use App\Models\Partner\Studio;
use App\Models\Partner\User;
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
        dump('seeding classes');

        ClassLesson::factory()->count(20)->create();

        $classes = ClassLesson::get();
        $class_types = ClassType::get();
        $studios = Studio::get();

        foreach($classes as $class) {
            $class_type = $class_types->random();
            $instructors = User::instructor()->inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $class->instructors()->sync($instructors);
            $class->update([
                'title' => ucfirst($class_type->title).' '.$class->duration,
                'class_type_id' => $class_type->id,
                'studio_id' => $studios->random()->id,
            ]);
        }
    }
}
