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

        $has_seeder_data = false;
        if (Storage::disk('seeders')->exists('/partner/data/business_'.$business_id.'/all.json')) {
            $data = json_decode(Storage::disk('seeders')->get('/partner/data/business_'.$business_id.'/all.json'));
            $has_seeder_data = true;
        }
        if($has_seeder_data == false){
            dump('NO class_types seeder');
            return;
        }
        $class_types = $data->class_types ?? null;
        if(!$class_types){
            dump('NO class_types data');
            return;
        }

        foreach ($class_types as $class_type) {
            ClassType::create([
                'status' => StateType::get('active'),
                'title' => $class_type->title,
                'description' => $class_type->title,
            ]);
        }

    }
}
