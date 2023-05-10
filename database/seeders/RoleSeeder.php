<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=RoleSeeder
     *
     * @return void
     */
    public function run()
    {
        if (!Storage::disk('seeders')->exists('data/roles.json')) {
            dump('data/roles.json file does not exist!');
            return;
        }
        $roles = json_decode(Storage::disk('seeders')->get('/data/roles.json'));        

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => Str::slug($role->title), 'source' => $role->source],
                ['title' => $role->title]
            );
        }
    }
}
