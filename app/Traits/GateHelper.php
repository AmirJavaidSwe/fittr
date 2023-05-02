<?php

namespace App\Traits;

use App\Models\SystemModule;
use Illuminate\Support\Facades\Gate;

trait GateHelper
{

    public function createGate($gateName, $for, $module, $permission)
    {
        return Gate::define($gateName, function () use ($for, $module, $permission) {

            $user = request()->user();

            $sysModule = SystemModule::where('slug', $module)->where('is_for', $for)->first();

            if ($sysModule) {
                return (auth()->user()->roles()->whereHas('permissions', function ($query) use ($permission, $sysModule) {
                    $query->where('system_module_id', $sysModule->id)->where('slug', $permission);
                })->count() > 0) ? true : false;
            }
            return false;
        });
    }
}
