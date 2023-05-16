<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Models\SystemModule;
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
        
        /* $roles = Role::whereIn('slug', ['super-admin', 'admin'])->get();
        $permissions = SystemModule::with('permissions')
        ->where('is_for', 'admin')
        ->get()
        ->pluck('permissions')
        ->flatten();
        $ids = [];
        foreach ($permissions as $permission) {
            $ids[] = $permission->id;
        }
        foreach ($roles as $role) {
            $role->permissions()->attach($ids);
        }
        
        $roles = Role::where('slug', 'partner')->get();

        $permissions = SystemModule::with('permissions')
        ->where('is_for', 'partner')
        ->get()
        ->pluck('permissions')
        ->flatten();
        $ids = [];
        foreach ($permissions as $permission) {
            $ids[] = $permission->id;
        }
        foreach ($roles as $role) {
            $role->permissions()->attach($ids);
        }
        */
    } 
}
