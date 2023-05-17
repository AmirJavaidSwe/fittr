<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Package;
use App\Enums\AppUserSource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\AdminUserRequest;

class SettingsController extends Controller
{
    public function index()
    {
        if (Gate::denies('viewAny-'.AppUserSource::admin->name . '-settings-viewAny')) {
            abort(403);
        }
        
        return Inertia::render('Admin/Settings', [
            'page_title' => __('Settings'),
            'header' => __('Settings'),
            'packages' => $this->getPackages(), // tab
            'admins' => $this->getAdminUsers(), // tab
        ]);
    }

    public function getPackages() {
        if (Gate::denies('viewAny-'.AppUserSource::admin->name . '-packages-viewAny')) {
            return [];
        }
        return Package::all();
    }
    
    public function getAdminUsers() {
        if (Gate::denies('viewAny-'.AppUserSource::admin->name . '-admin-users-viewAny')) {
            return [];
        }
        return User::admin()->select('id', 'name', 'email', 'is_super', 'profile_photo_path','created_at')->get();
    }

    public function addAdmins()
    {
        if (Gate::denies('create-'.AppUserSource::admin->name . '-admin-users-create')) {
            abort(403);
        }

        return Inertia::render('Admin/EditSettings/AddAdmin', [
            'page_title' => __('Add Admin'),
            'header' => __('Add Admin'),
            'roles' => Role::get(),
        ]);
    }

    public function saveAdmins(Request $request)
    {
        if (Gate::denies('create-'.AppUserSource::admin->name . '-admin-users-create')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => [
                'required',
                'string',
                'max:191',
                Rule::unique('users', 'email')->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return back()->withErrors($errors)->withInput();
        }

        $admin = new User();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->is_super = $request->is_super;
        $admin->source = auth()->user()->source;
        $admin->business_id = auth()->user()->business_id;
        $admin->save();
        $admin->roles()->sync($request->roles);

        session()->flash('flash_type', 'success');
        session()->flash('flash_timestamp', time());
        return redirect()->route('admin.settings')->with('flash_timestamp', time());
    }

    public function editAdmins($id)
    {
        if (Gate::denies('update-'.AppUserSource::admin->name . '-admin-users-update')) {
            abort(403);
        }

        return Inertia::render('Admin/EditSettings/EditAdmin', [
            'page_title' => __('Edit Admin'),
            'header' => __('Edit Admin'),
            'roles' => Role::get(),
            'admin' => User::admin()->with('roles')->where('id', $id)->first(), //tab
        ]);
    }

    public function updateAdmins(Request $request, $id)
    {
        if (Gate::denies('update-'.AppUserSource::admin->name . '-admin-users-update')) {
            abort(403);
        }
        
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
