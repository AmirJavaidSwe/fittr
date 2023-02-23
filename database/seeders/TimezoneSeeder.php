<?php

namespace Database\Seeders;

use DB;
use Storage;
use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=TimezoneSeeder
     *
     * @return void
     */
    public function run()
    {
        if (!Storage::disk('seeders')->exists('data/timezones_country.json')) {
            dump('data/timezones_country.json file does not exist!');
            return;
        }

        //get a list of Stripe supported countries
        $stripe_countries = Country::where('status', 1)->pluck('iso');

        $timezones = json_decode(\Storage::disk('seeders')->get('/data/timezones_country.json'));

        //create new colection for timezones
        $timezones_new = collect();
        foreach ($timezones as $key => $value) {
            if($key == 'ALL'){
                continue;
            }
            $is_stripe_supported = $stripe_countries->contains($key);
            for ($i=0; $i < count($value); $i++) {
                //Each country may have many timezones, creating record for each
                $timezones_new->push([
                    'iso' => $key,
                    'title' => $value[$i],
                    'status' => $is_stripe_supported,
                ]);
            }
        }
        DB::table('timezones')->truncate();
        DB::table('timezones')->insert($timezones_new->sortBy('iso')->all());
    }
}
