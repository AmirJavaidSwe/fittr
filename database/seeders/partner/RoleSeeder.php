<?php

namespace Database\Seeders\Partner;

use Storage;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=RoleSeeder
     *
     * @return void
     */
    public function run()
    {
        $business_id = config('database.connections.mysql_partner.business_id');
        dump('seeding roles');

        $has_seeder_data = false;
        if (Storage::disk('seeders')->exists('/partner/data/business_'.$business_id.'/all.json')) {
            $data = json_decode(Storage::disk('seeders')->get('/partner/data/business_'.$business_id.'/all.json'));
            $has_seeder_data = true;
        }
        if($has_seeder_data == false){
            dump('NO roles seeder');
            return;
        }
        $roles = $data->roles ?? null;
        if(!$roles){
            dump('NO roles data');
            return;
        }

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => Str::slug($role->title), 'source' => $role->source, 'business_id' => $business_id],
                ['title' => $role->title]
            );
        }
    }
}
