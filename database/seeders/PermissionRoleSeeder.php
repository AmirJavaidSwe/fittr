<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=RoleAndPermissionSeeder
     *
     * @return void
     */
    public function run()
    {

        DB::table('permission_role')->truncate();
        
        $roles = Role::where('slug', '<>', 'super-admin')->get();
        $permissions = Permission::pluck('id')->toArray();
        foreach ($roles as $role) {
            $role->permissions()->sync($permissions);
        }
    }
}
