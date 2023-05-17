<?php

namespace App\Http\Controllers\Partner;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\CreatePartnerUserRequest;
use App\Http\Requests\Partner\UpdatePartnerUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Partner/Users/Index', [
            'page_title' => __('Users'),
            'header' => __('Users'),
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
            'page_title' => __('Add User'),
            'header' => __('Add User'),
            'roles' => Role::where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePartnerUserRequest $request)
    {
        $request->merge([
            'password' => bcrypt($request->password),
            "source" => auth()->user()->source,
            'business_id' => auth()->user()->business_id
        ]);
        $user = User::create($request->all());
        $user->roles()->sync($request->roles);
        session()->flash('flash_type', 'success');
        session()->flash('flash_timestamp', time());
        return redirect()->route('partner.users.index')->with('flash_timestamp', time());
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
            'page_title' => __('Edit User'),
            'header' => __('Edit User'),
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
    public function update(UpdatePartnerUserRequest $request, $id)
    {
        $user = User::where('id', $id)
        ->where('source', auth()->user()->source)
        ->where('business_id', auth()->user()->business_id)->firstOrFail();
        if($user) {
            $user->update($request->all());
            $user->roles()->sync($request->roles);
        }
        session()->flash('flash_type', 'success');
        session()->flash('flash_timestamp', time());
        return redirect()->route('partner.users.index')->with('flash_timestamp', time());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)
        ->where('source', auth()->user()->source)
        ->where('business_id', auth()->user()->business_id)->firstOrFail();

        $user->delete();

        return $this->redirectBackSuccess(__('User deleted'), 'partner.users.index');
    }
}
