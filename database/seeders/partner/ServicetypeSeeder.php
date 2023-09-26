<?php

namespace Database\Seeders\Partner;

use App\Enums\StateType;
use App\Models\Partner\ServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ServicetypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $business_id = config('database.connections.mysql_partner.business_id');
        dump('seeding service_types');

        $has_seeder_data = false;
        if (Storage::disk('seeders')->exists('/partner/data/business_'.$business_id.'/all.json')) {
            $data = json_decode(Storage::disk('seeders')->get('/partner/data/business_'.$business_id.'/all.json'));
            $has_seeder_data = true;
        }
        if($has_seeder_data == false){
            dump('NO service_types seeder');
            return;
        }
        $service_types = $data->service_types ?? null;
        if(!$service_types){
            dump('NO service_types data');
            return;
        }

        foreach ($service_types as $service_type) {
            ServiceType::create([
                'status' => StateType::get('active'),
                'title' => $service_type->title,
                'description' => $service_type->description,
            ]);
        }
    }
}
