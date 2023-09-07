<?php

namespace Database\Seeders\Partner;

use App\Models\User;
use App\Models\Partner\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $business_id = config('database.connections.mysql_partner.business_id');
        dump('seeding locations');

        if (!Storage::disk('seeders')->exists('/partner/data/locations.json')) {
            dump('/partner/data/locations.json file does not exist!');
            return;
        }
        $locations = json_decode(Storage::disk('seeders')->get('/partner/data/locations.json'));
        foreach ($locations as $location) {
            $location_model = Location::create([
                'status' => $location->status,
                'title' => $location->title,
                'brief' => $location->brief,
                'address_line_1' => $location->address_line_1,
                'address_line_2' => $location->address_line_2,
                'country_id' => $location->country_id,
                'city' => $location->city,
                'postcode' => $location->postcode,
                'map_latitude' => $location->map_latitude,
                'map_longitude' => $location->map_longitude,
                'tel' => $location->tel,
                'email' => $location->email,
                'manager_id' => User::where('business_id', $business_id)->first()->id,
            ]);
            if(!empty($location->studios)){
                dump('seeding location '.$location->title.' studios');
                foreach ($location->studios as $studio){
                    $location_model->studios()->create([
                        'status' => $studio->status,
                        'title' => $studio->title,
                    ]);
                }
            }
        }

        // Location::factory()
        //     ->count(5)
        //     ->sequence(
        //         ['title' => 'Wandsworth'],
        //         ['title' => 'Borough'],
        //         ['title' => 'Richmond'],
        //         ['title' => 'Moorgate'],
        //         ['title' => 'Bank'],
        //         ['title' => 'Covent Garden'],
        //     )
        //     ->create();
    }
}
