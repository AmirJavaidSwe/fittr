<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\PartnerUserRequest;
use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;

class PartnerUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
            'users' => User::select('id', 'name', 'email', 'is_super', 'profile_photo_path','created_at')
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
            'password' => bcrypt($request->password),
            "source" => auth()->user()->source,
            'business_id' => auth()->user()->business_id
        ]);
        $user = User::create($fields);
        $user->roles()->sync($request->roles);

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
        if($request->filled('password')){
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

        if($user->id == auth()->user()->id){
            return $this->redirectBackError(__('You can not delete yourself'));
        }

        $user->roles()->detach();
        $user->delete();

        return $this->redirectBackSuccess(__('User deleted'), 'partner.users.index');
    }
}
