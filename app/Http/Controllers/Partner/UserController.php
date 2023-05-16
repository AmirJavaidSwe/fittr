<?php

namespace App\Http\Controllers\Partner;

use App\Models\Package;
use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\SystemModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Partner/Users/Index', [
            'page_title' => __('Users'),
            'header' => __('Users'),
            'admins' => User::admin()
                ->select('id', 'name', 'email', 'is_super', 'profile_photo_path','created_at')
                ->where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Partner/Users/Create', [
            'page_title' => __('Add User'),
            'header' => __('Add User'),
            'roles' => Role::where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $role = Role::create($request->only('title'));
        if(auth()->user()->source) {
            $role->source = auth()->user()->source;
            $role->business_id = auth()->user()->business_id;
            $role->save();
        }
        $role->permissions()->sync($request->permissions);
        return $this->redirectBackSuccess(__('Role created'));
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $role = Role::where('slug', $slug)
        ->where('source', auth()->user()->source);
        if(auth()->user()->business_id) {
            $role = $role->where('business_id', auth()->user()->business_id);
        }
        $role = $role->with(['business'])
        ->firstOrFail();
        return Inertia::render('Roles/Show', [
            'role' => $role,
            'page_title' => __('Role details'),
            'header' => __('Role details'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($slug)
    {
        $modules = SystemModule::with('permissions')->where('is_for', auth()->user()->source)->get();
        $role = Role::with('permissions')
        ->where('source', auth()->user()->source);
        if(auth()->user()->business_id) {
            $role = $role->where('business_id', auth()->user()->business_id);
        }
        $role = $role->with(['business'])
        ->where('slug', $slug)->firstOrFail();
        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'modules' => $modules,
            'page_title' => __('Role details'),
            'header' => __('Update role details'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, $slug)
    {
        $role = Role::where('slug', $slug)
        ->where('source', auth()->user()->source);
        if(auth()->user()->business_id) {
            $role = $role->where('business_id', auth()->user()->business_id);
        }
        $role = $role->firstOrFail();
        $role->update($request->only('title'));
        $role->permissions()->sync($request->permissions);
        return $this->redirectBackSuccess(__('Role updated'), auth()->user()->source.'.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $role = Role::where('slug', $slug)
        ->where('source', auth()->user()->source);
        if(auth()->user()->business_id) {
            $role = $role->where('business_id', auth()->user()->business_id);
        }
        $role = $role->firstOrFail();

        $role->delete();

        return $this->redirectBackSuccess(__('Role deleted'), auth()->user()->source.'.roles.index');
    }
}
