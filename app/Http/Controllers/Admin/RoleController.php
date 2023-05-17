<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Enums\AppUserSource;
use App\Models\SystemModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
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
        if (Gate::denies('viewAny-'.AppUserSource::admin->name . '-roles-viewAny')) {
            abort(403);
        }
        
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'created_at');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Roles/Index', [
            'roles' => Role::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where('title', 'LIKE', '%' . $this->search . '%');
                })
                ->when(auth()->user()->source, function ($query) {
                    $query->where('source', auth()->user()->source);
                    if(auth()->user()->business_id) {
                        $query = $query->where('business_id', auth()->user()->business_id);
                    }
                })
                ->paginate($this->per_page)
                ->withQueryString()
                //AbstractPaginator@through(), transforms chunk
                ->through(fn ($role) => [
                    'id' => $role->id,
                    'title' => $role->title,
                    'slug' => $role->slug,
                    'created_at' => $role->created_at,
                    'url_show' => URL::route(auth()->user()->source.'.roles.show', $role->slug),
                    'url_edit' => URL::route(auth()->user()->source.'.roles.edit', $role->slug),
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
        if (Gate::denies('create-'.AppUserSource::admin->name . '-roles-create')) {
            abort(403);
        }

        $modules = SystemModule::with('permissions')->where('is_for', auth()->user()->source)->get();
        return Inertia::render('Roles/Create', [
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
        if (Gate::denies('create-'.AppUserSource::admin->name . '-roles-create')) {
            abort(403);
        }

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
        if (Gate::denies('view-'.AppUserSource::admin->name . '-roles-view')) {
            abort(403);
        }

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
        if (Gate::denies('update-'.AppUserSource::admin->name . '-roles-update')) {
            abort(403);
        }

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
        if (Gate::denies('update-'.AppUserSource::admin->name . '-roles-update')) {
            abort(403);
        }

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
        if (Gate::denies('destroy-'.AppUserSource::admin->name . '-roles-destroy')) {
            abort(403);
        }

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
