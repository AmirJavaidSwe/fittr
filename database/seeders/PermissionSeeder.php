<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=RoleAndPermissionSeeder
     *
     * @return void
     */
    public function run()
    {

        Permission::truncate();
        $routes = Route::getRoutes()->getRoutes();

        foreach ($routes as $route) {
            if ($route->getName() != '' && $route->getAction()['middleware']['0'] == 'web') {
                $slug = strtolower(str_replace(['.', ' ', '-', '_'], '-', $route->getName()));
                if (!(Permission::where('slug', $slug)->exists())) {
                    Permission::create([
                        'name' => ucwords(strtolower(str_replace(['.', ' ', '-', '_'], ' ', $route->getName()))),
                        'slug' => $slug
                    ]);
                }
            }
        }
    }
}
