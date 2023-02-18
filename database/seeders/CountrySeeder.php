<?php

namespace Database\Seeders;

use DB;
use Storage;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=CountrySeeder
     *
     * @return void
     */
    public function run()
    {
        if (!Storage::disk('seeders')->exists('data/countries_full.json')) {
            dump('data/countries_full.json file does not exist!');
            return;
        }
        $countries = json_decode(Storage::disk('seeders')->get('/data/countries_full.json'));
        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'name' => $country->name,
                'iso' => $country->iso,
                'dial_code' => $country->dial_code ?? null,
                'currency' => $country->currency,
                'mask' => (is_array($country->mask) ? $country->mask[0] : $country->mask),
                'status' => $country->status ?? false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
