<?php

namespace App\Http\Controllers\Partner;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Models\SystemModule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\PartnerUserRequest;

class PartnerUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;

    public function index(Request $request)
    {

        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Partner/Users/Index', [
            'page_title' => __('Business Settings - Users'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Users'),
                    'link' => null,
                ],
            ),
            'roles' => Role::where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)->get(),
            'systemModules' => SystemModule::with('permissions')->where('is_for', auth()->user()->source)->get(),
            'users' => User::select('id', 'name', 'email', 'is_super', 'profile_photo_path', 'created_at')
                ->where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)->orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function ($query) {
                        $query->orWhere('id', intval($this->search))
                            ->orWhere('name', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('email', 'LIKE', '%' . $this->search . '%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Partner/Users/Create', [
            'page_title' => __('Business Settings - Add User'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Users'),
                    'link' => route('partner.users.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Add User'),
                    'link' => null,
                ],
            ),
            'roles' => Role::where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PartnerUserRequest $request)
    {
        $fields = array_merge($request->validated(), [
            // 'password' => bcrypt($request->password),
            "source" => auth()->user()->source,
            'business_id' => auth()->user()->business_id
        ]);

        $user = User::create($fields);
        $user->roles()->sync($request->roles);

        $user->sendEmailVerificationNotification();

        if(request()->has('returnTo')) {
            $extra = array('gm' => $user);
            return redirect()->route(request()->returnTo)->with('extra', $extra);
        }

        return $this->redirectBackSuccess(__('User created'), 'partner.users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Inertia::render('Partner/Users/Show', [
            'page_title' => __('Show User'),
            'header' => __('Show User'),
            'user' => User::with('roles')
                ->where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)
                ->where('id', $id)->firstOrFail()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return Inertia::render('Partner/Users/Edit', [
            'page_title' => __('Business Settings - Edit User'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Users'),
                    'link' => route('partner.users.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit User'),
                    'link' => null,
                ],
            ),
            'roles' => Role::where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)->get(),
            'editUser' => User::with('roles')->where('id', $id)
                ->where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)
                ->firstOrFail(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PartnerUserRequest $request, $id)
    {
        $validated = $request->validated();
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        } else {
            unset($validated['password']);
        }
        $user = User::where('id', $id)
            ->where('source', auth()->user()->source)
            ->where('business_id', auth()->user()->business_id)->firstOrFail();

        $user->update($validated);
        $user->roles()->sync($request->roles);

        return $this->redirectBackSuccess(__('User updated'), 'partner.users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)
            ->where('source', auth()->user()->source)
            ->where('business_id', auth()->user()->business_id)->firstOrFail();

        if ($user->id == auth()->user()->id) {
            return $this->redirectBackError(__('You can not delete yourself'));
        }

        $user->roles()->detach();
        $user->delete();

        return $this->redirectBackSuccess(__('User deleted'), 'partner.users.index');
    }
}
