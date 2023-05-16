<?php

namespace App\Http\Controllers\Partner;

use App\Models\Package;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
            'admins' => User::select('id', 'name', 'email', 'is_super', 'profile_photo_path','created_at')
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
    public function store(Request $request)
    {
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
            'roles' => Role::where('source', auth()->user()->source)
                ->where('business_id', auth()->user()->business_id)->get(),
            'admin' => User::with('roles')->where('id', $id)->first(), //tab
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
            'admin' => User::with('roles')->where('id', $id)->first(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
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

        $admin = User::where('id', $id)->first();
        if($admin) {
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->is_super = $request->is_super;
            $admin->save();
            $admin->roles()->sync($request->roles);
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
        ->where('source', auth()->user()->source);
        if(auth()->user()->business_id) {
            $user = $user->where('business_id', auth()->user()->business_id);
        }
        $user = $user->firstOrFail();

        $user->delete();

        return $this->redirectBackSuccess(__('User deleted'), 'partner.users.index');
    }
}
