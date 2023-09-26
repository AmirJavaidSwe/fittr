<?php

namespace Database\Seeders\Partner;

use Storage;
use App\Models\Partner\Pack;
use App\Models\Partner\PackPrice;
use Illuminate\Database\Seeder;

class PackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $business_id = config('database.connections.mysql_partner.business_id');
        dump('seeding packs');
        $has_seeder_data = false;
        if (Storage::disk('seeders')->exists('/partner/data/business_'.$business_id.'/all.json')) {
            $data = json_decode(Storage::disk('seeders')->get('/partner/data/business_'.$business_id.'/all.json'));
            $has_seeder_data = true;
        }
        if($has_seeder_data == false){
            dump('NO packs seeder');
            return;
        }
        $packs = $data->sandboxpacks ?? null;
        if(!$packs){
            dump('NO packs data');
            return;
        }

        Pack::truncate();
        foreach ($packs as $pack) {
            $pack_model = Pack::create([
                'type' => $pack->type,
                'title' => $pack->title,
                'sub_title' => $pack->sub_title,
                'description' => $pack->description,
                'stripe_product_id' => $pack->stripe_product_id,
                'is_active' => $pack->is_active,
                'is_restricted' => $pack->is_restricted,
                'is_private' => $pack->is_private,
                'restrictions' => $pack->restrictions,
                'private_url' => $pack->private_url,
                'active_from' => $pack->active_from,
                'active_to' => $pack->active_to,
                'created_at' => $pack->created_at,
                'updated_at' => $pack->updated_at,
            ]);
            $prices = $pack->prices ?? [];

            foreach ($prices as $p) {
                $pack_price =  new PackPrice((array) $p);
                $pack_model->pack_prices()->save($pack_price);
            }
        }
    }
}
