<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemModule;
use App\Models\Permission;


class AddSystemModulesAndPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-system-modules-and-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modules_and_permissions = [
            'dashboard' => [
                'index'
            ],
            'Partner performance' => [
                'index'
            ],
            'Partner management' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore'
            ],
            'AWS Instances' => [
            ],
            'Settings' => [
                'index'
            ],
            'Packages' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore',
            ],
            'Admin Users' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore',
            ],
            'Roles' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore',
            ],
            'Classes' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore',
                'import',
                'export',
            ],
            'Members' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore',
                'import',
                'export',
            ],
            'Instructors' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore',
                'import',
                'export',
            ],
            'Pricing' => [
                'index',
            ],
            'Partner Settings' => [
                'index',
            ],
            'Business' => [
                'index',
                'edit',
            ],
            'Tax' => [
                'index',
            ],
            'External App' => [
                'index',
            ],
            'Team' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore',
                'import',
                'export',
            ],
            'Studio Class Type' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore',
                'import',
                'export',
            ],
            'Studio Amenities' => [
                'index',
                'create',
                'edit',
                'show',
                'delete',
                'restore',
                'import',
                'export',
            ],
            'Web Store' => [
                'index',
                'edit',
            ],
            'Waivers' => [
                'index',
                'edit',
            ],
            'Business Payments' => [
                'list',
            ],
        ];

        foreach ($modules_and_permissions as $key => $value) {
            $name = ucwords(trim($key));
            $slug = str_replace(' ', '-', strtolower(trim($key)));
            $module = SystemModule::where('slug', $slug)->first();
            if(!$module) {
                $module = SystemModule::create([
                    'name' => ucwords(trim($key)),
                    'slug' => str_replace(' ', '-', strtolower($key)),
                ]);
            }
            if(!empty($value)) {
                foreach ($value as $k => $v) {
                    $name = ucwords(trim($v));
                    $slug = str_replace(' ', '-', strtolower(trim($v)));
                    if(!(Permission::where('system_module_id', $module->id)->where('slug', $slug)->exists())) {
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
