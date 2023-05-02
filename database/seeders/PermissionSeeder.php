<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Models\SystemModule;
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

        $modules_and_permissions = [
            'admin' => [
                'dashboard' => [
                    'View'
                ],
                'Partner performance' => [
                    'View'
                ],
                'Partner management' => [
                    'View',
                    'Create',
                    'Edit',
                    'view Any',
                    'Delete',
                    'Restore'
                ],
                'AWS Instances' => [],
                'Settings' => [
                    'View'
                ],
                'Packages' => [
                    'View',
                    'Create',
                    'Edit',
                    'view Any',
                    'Delete',
                    'Restore'
                ],
                'Admin Users' => [
                    'View',
                    'Create',
                    'Edit',
                    'view Any',
                    'Delete',
                    'Restore'
                ],
                'Roles' => [
                    'View',
                    'Create',
                    'Edit',
                    'view Any',
                    'Delete',
                    'Restore'
                ],
            ],
            'partner' => [
                'Classes' => [
                    'View',
                    'Create',
                    'Edit',
                    'view Any',
                    'Delete',
                    'Restore',
                    'Import',
                    'Export',
                ],
                'Members' => [
                    'View',
                    'Create',
                    'Edit',
                    'view Any',
                    'Delete',
                    'Restore',
                    'Import',
                    'Export',
                ],
                'Instructors' => [
                    'View',
                    'Create',
                    'Edit',
                    'view Any',
                    'Delete',
                    'Restore',
                    'Import',
                    'Export',
                ],
                'Pricing' => [
                    'View',
                ],
                'Partner Settings' => [
                    'View',
                ],
                'Business' => [
                    'View',
                    'Edit'
                ],
                'Tax' => [
                    'View',
                ],
                'External App' => [
                    'View',
                ],
                'Team' => [
                    'View',
                    'Create',
                    'Edit',
                    'can-show',
                    'Delete',
                    'Restore',
                    'Import',
                    'Export',
                ],
                'Studio Class Type' => [
                    'View',
                    'Create',
                    'Edit',
                    'can-show',
                    'Delete',
                    'Restore',
                    'Import',
                    'Export',
                ],
                'Studio Amenities' => [
                    'View',
                    'Create',
                    'Edit',
                    'can-show',
                    'Delete',
                    'Restore',
                    'Import',
                    'Export',
                ],
                'Web Store' => [
                    'View',
                    'Edit',
                ],
                'Waivers' => [
                    'View',
                    'Edit',
                ],
                'Business Payments' => [
                    'View'
                ],
            ]
        ];

        foreach ($modules_and_permissions as $moduleFor => $modulesData) {
            foreach ($modulesData as $key => $value) {
                $name = ucwords(trim($key));
                $slug = str_replace(' ', '-', strtolower(trim($key)));
                $module = SystemModule::where('slug', $slug)->where('is_for', $moduleFor)->first();
                if (!$module) {
                    $module = SystemModule::create([
                        'name' => ucwords(trim($key)),
                        'slug' => str_replace(' ', '-', strtolower($key)),
                        'is_for' => $moduleFor,
                    ]);
                }
                if (!empty($value)) {
                    foreach ($value as $k => $v) {
                        $name = ucwords(trim($v));
                        $slug = str_replace(' ', '-', strtolower(trim($v)));
                        if (!(Permission::where('system_module_id', $module->id)->where('slug', $slug)->exists())) {
                            Permission::create([
                                'name' => $name,
                                'slug' => $slug,
                                'system_module_id' => $module->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
