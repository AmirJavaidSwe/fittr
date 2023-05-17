<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Models\SystemModule;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=PermissionSeeder
     *
     * @return void
     */
    public function run()
    {
        // Controller to Policy mapping:
        // index => viewAny
        // show => view
        // create, store => create
        // edit, update => update
        // destroy => destroy
        // restore => restore
        // import => import
        // export => export

        $modules_and_permissions = [
            'admin' => [
                'dashboard' => [
                    'viewAny',
                ],
                'Partners performance' => [
                    'viewAny',
                ],
                'Partner management' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'destroy',
                    'restore',
                    'loginAs',
                ],
                "AWS Instances" => [
                    'viewAny',
                    'view',
                    'showMetric'
                ],
                'Settings' => [
                    'viewAny',
                ],
                'Packages' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'destroy',
                    'restore',
                ],
                'Admin Users' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                ],
                'Roles' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                ],
            ],
            'partner' => [
                'dashboard' => [
                    'viewAny',
                ],
                'Users' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                ],
                'Roles' => [
                    'viewAny',
                    'view',
                    'create',
                    'update'
                ],
                'Classes' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'destroy',
                    'restore',
                    'import',
                    'export',
                ],
                'Members' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'destroy',
                    'restore',
                    'import',
                    'export',
                ],
                'Instructors' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'destroy',
                    'restore',
                    'import',
                    'export',
                ],
                'Partner Settings' => [
                    'viewAny',
                ],
                'Business Settings' => [
                    'viewAny',
                    'view',
                    'update',
                ],
                'Studio Class Type' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'destroy',
                    'restore',
                ],
                'Studio Amenities' => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'destroy',
                    'restore',
                ],
            ]
        ];

        foreach ($modules_and_permissions as $moduleFor => $modulesData) {
            foreach ($modulesData as $key => $value) {
                $title = ucwords(trim($key));
                $slug = Str::slug($title);

                $module = SystemModule::updateOrCreate(
                    ['slug' => $slug, 'is_for' => $moduleFor],
                    ['title' => $title]
                );

                if (!empty($value)) {
                    foreach ($value as $policy_method) {
                        if (!(Permission::where('system_module_id', $module->id)->where('slug', $policy_method)->exists())) {
                            Permission::create([
                                'title' => ucwords(trim($policy_method)),
                                'slug' => $policy_method,
                                'system_module_id' => $module->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
