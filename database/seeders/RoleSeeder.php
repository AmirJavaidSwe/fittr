<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=RoleAndPermissionSeeder
     *
     * @return void
     */
    public function run()
    {

        $roles = [
            'Super Admin',
            'Admin',
            'Partner'
        ];

        foreach ($roles as $role) {
            if (!(Role::where('slug', Str::slug($role))->exists())) {
                Role::create([
                    'name' => $role,
                    'slug' => Str::slug($role),
                    'created_by' => null,
                ]);
            }
        }
    }
}
