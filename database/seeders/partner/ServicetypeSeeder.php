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

        if (!Storage::disk('seeders')->exists('/partner/data/service_types.json')) {
            dump('/partner/data/service_types.json file does not exist!');
            return;
        }
        $service_types = json_decode(Storage::disk('seeders')->get('/partner/data/service_types.json'));
        foreach ($service_types as $service_type) {
            ServiceType::create([
                'status' => StateType::get('active'),
                'title' => $service_type->title,
                'description' => $service_type->description,
            ]);
        }

        // ServiceType::factory()
        //     ->count(3)
        //     ->sequence(
        //         ['title' => 'body hydration'],
        //         ['title' => 'massage'],
        //         ['title' => 'personal trainer'],
        //     )
        //     ->create();
    }
}
