<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $business_id = config('database.connections.mysql_partner.business_id');
        dump('seeding amenities');

        if (!Storage::disk('seeders')->exists('/partner/data/amenities.json')) {
            dump('/partner/data/amenities.json file does not exist!');
            return;
        }
        $amenities = json_decode(Storage::disk('seeders')->get('/partner/data/amenities.json'));
        foreach ($amenities as $amenity) {
            Amenity::create([
                'title' => $amenity->title,
                'status' => $amenity->status,
            ]);
        }
    }
}
