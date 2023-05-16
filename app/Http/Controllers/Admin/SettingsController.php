<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings', [
            'page_title' => __('Settings'),
            'header' => __('Settings'),
            'packages' => Package::all(), //tab
            'admins' => User::admin()->select('id', 'name', 'email', 'is_super', 'profile_photo_path','created_at')->get(), // tab
        ]);
    }
    public function editAdmins($id)
    {
        return Inertia::render('Admin/EditSettings/EditAdmin', [
            'page_title' => __('Edit Admin'),
            'header' => __('Edit Admin'),
            'roles' => Role::get(),
            'admin' => User::admin()->with('roles')->where('id', $id)->first(), //tab
        ]);
    }
    
    public function updateAdmins(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => [
                'required',
                'string',
                'max:191',
                Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return back()->withErrors($errors)->withInput();
        }

        $admin = User::admin()->where('id', $id)->first();
        if($admin) {
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->is_super = $request->is_super;
            $admin->save();
            $admin->roles()->sync($request->roles);
        }
        session()->flash('flash_type', 'success');
        session()->flash('flash_timestamp', time());
        return redirect()->route('admin.settings')->with('flash_timestamp', time());
    }
}
