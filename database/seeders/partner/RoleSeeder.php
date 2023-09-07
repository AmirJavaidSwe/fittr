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

        if (!Storage::disk('seeders')->exists('/partner/data/roles.json')) {
            dump('/partner/data/roles.json file does not exist!');
            return;
        }
        $roles = json_decode(Storage::disk('seeders')->get('/partner/data/roles.json'));

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => Str::slug($role->title), 'source' => $role->source, 'business_id' => $business_id],
                ['title' => $role->title]
            );
        }
    }
}
