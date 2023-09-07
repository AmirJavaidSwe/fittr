<?php

namespace Database\Seeders\Partner;

use App\Enums\StateType;
use App\Models\Partner\ClassType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ClasstypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $business_id = config('database.connections.mysql_partner.business_id');
        dump('seeding class_types');

        if (!Storage::disk('seeders')->exists('/partner/data/class_types.json')) {
            dump('/partner/data/class_types.json file does not exist!');
            return;
        }
        $class_types = json_decode(Storage::disk('seeders')->get('/partner/data/class_types.json'));
        foreach ($class_types as $class_type) {
            ClassType::create([
                'status' => StateType::get('active'),
                'title' => $class_type->title,
                'description' => $class_type->title,
            ]);
        }

        // ClassType::factory()
        //     ->count(9)
        //     ->sequence(
        //         ['title' => 'cycle'],
        //         ['title' => 'circuits'],
        //         ['title' => 'yoga'],
        //         ['title' => 'barre'],
        //         ['title' => 'f.i.t'],
        //         ['title' => 'dance'],
        //         ['title' => 'strength'],
        //         ['title' => 'wall climbing'],
        //         ['title' => 'water aerobics'],
        //         ['title' => 'fusion'],
        //         ['title' => 'breathwork'],
        //         ['title' => 'stretch & mobility'],
        //         ['title' => 'high performance'],
        //     )
        //     ->create();
    }
}
