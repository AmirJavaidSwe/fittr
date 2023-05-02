<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\SystemModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;

class RoleController extends Controller
{
    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Gate::authorize('viewAny', Role::class);

        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'created_at');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where('name', 'LIKE', '%' . $this->search . '%');
                })
                ->paginate($this->per_page)
                ->withQueryString()
                //AbstractPaginator@through(), transforms chunk
                ->through(fn ($role, $index) => [
                    'indexing' => ++$index,
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'created_at' => $role->created_at,
                    // 'created_by' => $this->createdBy($role),
                    'url_show' => URL::route('admin.roles.show', $role->slug),
                    'url_edit' => URL::route('admin.roles.edit', $role->slug),
                ]),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Roles'),
            'header' => __('Roles management'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Gate::authorize('create', Role::class);

        $modules = SystemModule::with('permissions')->get();
        return Inertia::render('Admin/Roles/Create', [
            'modules' => $modules,
            'page_title' => __('New role'),
            'header' => __('New role'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        // Gate::authorize('create', Role::class);
        $request->merge(['slug' => Str::slug($request->name)]);

        $role = Role::where('slug', $request->slug)->first();
        if(!$role) {
            $role = Role::create($request->only('name', 'slug'));
        }

        if(count($request->permissions)) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('flash_type', 'success')->with('flash_timestamp', time());
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        // Gate::authorize('view', Role::class);
        $role = Role::where('slug', $slug)->firstOrFail();
        $role->created_by = $this->createdBy($role);
        return Inertia::render('Admin/Roles/Show', [
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
        // Gate::authorize('update', Role::class);
        $modules = SystemModule::with('permissions')->get();
        $role = Role::with('permissions')->where('slug', $slug)->firstOrFail();
        return Inertia::render('Admin/Roles/Edit', [
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
        $role = Role::where('slug', $slug)->firstOrFail();
        $request->merge(['slug' => Str::slug($request->name)]);
        $role->update($request->only('name', 'slug'));
        if(count($request->permissions)) {
            $role->permissions()->sync($request->permissions);
        }
        session()->flash('flash_type', 'success');
        session()->flash('flash_timestamp', time());
        return redirect()->route('admin.roles.index')->with('flash_timestamp', time());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        // Gate::authorize('destroy', Role::class);

        $role = Role::where('slug', $slug)->firstOrFail();
        
        $role->delete();

        return redirect()->route('admin.roles.index')->with('flash_timestamp', time());
    }

    public function createdBy($role) {
        $user = User::find($role->created_by);
        if($user) {
            $created_by = $user->name;
            if($user->role) {
                $created_by .= ' ('.ucwords($user->role).')';
            }
            if((!is_null($user->business_id)) && ($user->business && $user->business->name)) {
                $created_by .= ' ('.ucwords($user->business->name).')';
            }
    
            return $created_by;
        }
        return null;
    }
}
