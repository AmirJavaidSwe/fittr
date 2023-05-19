<?php

namespace App\Http\Controllers\Shared;

use App\Enums\AppUserSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\RoleRequest;
use App\Models\Role;
use App\Models\SystemModule;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

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

        $user = auth()->user();
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'created_at');
        $this->order_dir = $request->query('order_dir', 'desc');

        switch ($user->source) {
            case AppUserSource::admin->name:
                $header = __('Roles management');
                break;
            case AppUserSource::partner->name:
                $header = array(
                    [
                        'title' => __('Settings'),
                        'link' => route('partner.settings.index'),
                    ],
                    [
                        'title' => '/',
                        'link' => null,
                    ],
                    [
                        'title' => __('Roles'),
                        'link' => null,
                    ],
                );
                break;
        }

        return Inertia::render('Roles/Index', [
            'roles' => Role::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where('title', 'LIKE', '%' . $this->search . '%');
                })
                ->where('source', $user->source)
                ->when(!empty($user->business_id), function (Builder $query) use ($user) {
                    $query = $query->where('business_id', $user->business_id);
                })
                ->paginate($this->per_page)
                ->withQueryString()
                //AbstractPaginator@through(), transforms chunk
                ->through(fn ($role) => [
                    'id' => $role->id,
                    'title' => $role->title,
                    'slug' => $role->slug,
                    'created_at' => $role->created_at,
                    'url_show' => URL::route($user->source.'.roles.show', $role->slug),
                    'url_edit' => URL::route($user->source.'.roles.edit', $role->slug),
                ]),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Roles'),
            'header' => $header ?? null,
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

        $user = auth()->user();
        switch ($user->source) {
            case AppUserSource::admin->name:
                $header = __('Create new role');
                break;
            case AppUserSource::partner->name:
                $header = array(
                    [
                        'title' => __('Settings'),
                        'link' => route('partner.settings.index'),
                    ],
                    [
                        'title' => '/',
                        'link' => null,
                    ],
                    [
                        'title' => __('Roles'),
                        'link' => route('partner.roles.index'),
                    ],
                    [
                        'title' => '/',
                        'link' => null,
                    ],
                    [
                        'title' => __('Create new'),
                        'link' => null,
                    ],
                );
                break;
        }

        $modules = SystemModule::with('permissions')->where('is_for', auth()->user()->source)->get();
        return Inertia::render('Roles/Create', [
            'modules' => $modules,
            'page_title' => __('New role'),
            'header' => $header,
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
        $user = auth()->user();
        $role = Role::create($request->validated());
        $role->source = $user->source;
        $role->business_id = $user->business_id;
        $role->save();

        $role->permissions()->sync($request->permissions);

        return $this->redirectBackSuccess(__('Role created'), $user->source.'.roles.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        if (Gate::denies('view-'.AppUserSource::admin->name . '-roles-view')) {
            abort(403);
        }

        $user = auth()->user();
        $role = Role::with(['business', 'permissions'])
            ->where('source', $user->source)
            ->where('slug', $slug)
            ->when(!empty($user->business_id), function (Builder $query) use ($user) {
                $query->where('business_id', $user->business_id);
            })
            ->firstOrFail();

        switch ($user->source) {
            case AppUserSource::admin->name:
                $header = __('Role details');
                break;
            case AppUserSource::partner->name:
                $header = array(
                    [
                        'title' => __('Settings'),
                        'link' => route('partner.settings.index'),
                    ],
                    [
                        'title' => '/',
                        'link' => null,
                    ],
                    [
                        'title' => __('Roles'),
                        'link' => route('partner.roles.index'),
                    ],
                    [
                        'title' => '/',
                        'link' => null,
                    ],
                    [
                        'title' => __('Role details'),
                        'link' => null,
                    ],
                );
                break;
        }

        return Inertia::render('Roles/Show', [
            'role' => $role,
            'page_title' => __('Role details'),
            'header' => $header,
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
        $user = auth()->user();
        $modules = SystemModule::with('permissions')->where('is_for', $user->source)->get();
        $role = Role::with(['business', 'permissions'])
            ->where('source', $user->source)
            ->where('slug', $slug)
            ->when(!empty($user->business_id), function (Builder $query) use ($user) {
                $query->where('business_id', $user->business_id);
            })
            ->firstOrFail();

        switch ($user->source) {
            case AppUserSource::admin->name:
                $header = __('Edit role');
                break;
            case AppUserSource::partner->name:
                $header = array(
                    [
                        'title' => __('Settings'),
                        'link' => route('partner.settings.index'),
                    ],
                    [
                        'title' => '/',
                        'link' => null,
                    ],
                    [
                        'title' => __('Roles'),
                        'link' => route('partner.roles.index'),
                    ],
                    [
                        'title' => '/',
                        'link' => null,
                    ],
                    [
                        'title' => __('Edit role'),
                        'link' => null,
                    ],
                );
                break;
        }

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'modules' => $modules,
            'page_title' => __('Role details'),
            'header' => $header,
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

        $user = auth()->user();
        $role = Role::where('source', $user->source)
            ->where('slug', $slug)
            ->when(!empty($user->business_id), function (Builder $query) use ($user) {
                $query->where('business_id', $user->business_id);
            })
            ->firstOrFail();
        $role->update($request->only('title'));

        $role->permissions()->sync($request->permissions);

        return $this->redirectBackSuccess(__('Role updated'), $user->source.'.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        if (Gate::denies('destroy-'.AppUserSource::admin->name . '-roles-destroy')) {
            abort(403);
        }

        $user = auth()->user();
        $role = Role::where('source', $user->source)
            ->where('slug', $slug)
            ->when(!empty($user->business_id), function (Builder $query) use ($user) {
                $query->where('business_id', $user->business_id);
            })
            ->firstOrFail();

        $role->permissions()->detach();
        $role->delete();

        return $this->redirectBackSuccess(__('Role deleted'), $user->source.'.roles.index');
    }
}
