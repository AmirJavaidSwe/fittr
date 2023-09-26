<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Enums\AppUserSource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PartnerController extends Controller
{
    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;

    public function index(Request $request)
    {
        if (Gate::denies('viewAny-' . AppUserSource::get('admin'). '-partner-management-viewAny')) {
            abort(403);
        }
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Admin/Partners', [
            'users' => User::partner()
                ->orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function ($query) {
                        $query
                            ->orWhere('id', intval($this->search))
                            ->orWhere('first_name', 'LIKE', '%'.$this->search.'%')
                            ->orWhere('last_name', 'LIKE', '%'.$this->search.'%')
                            ->orWhere('email', 'LIKE', '%' . $this->search . '%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString()
                //AbstractPaginator@through(), transforms chunk
                ->through(
                    fn ($user) => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'initials' => $user->initials,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'business_id' => $user->business_id,
                        'business_name' => $user->business?->settings->firstWhere('key', 'business_name')?->val,
                        'created_at' => $user->created_at->format('Y-m-d'),
                    ],
                ),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Partners'),
            'header' => __('Partners management'),
        ]);
    }

    public function show(Request $request, $id)
    {
        if (Gate::denies('view-' . AppUserSource::get('admin'). '-partner-management-view')) {
            abort(403);
        }
        $partner = User::partner()->findOrFail($id);

        return Inertia::render('Admin/PartnerShow', [
            'partner' => $partner,
            'page_title' => __('Partner details'),
            'header' => __('Partner details'),
        ]);
    }

    public function edit(Request $request, $id)
    {
        if (Gate::denies('update-' . AppUserSource::get('admin'). '-partner-management-update')) {
            abort(403);
        }
        $partner = User::partner()->findOrFail($id);

        return Inertia::render('Admin/PartnerEdit', [
            'partner' => $partner,
            'page_title' => __('Partner details'),
            'header' => __('Update partner details'),
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('update-' . AppUserSource::get('admin'). '-partner-management-update')) {
            abort(403);
        }
        $validated = $request->validateWithBag('updateProfileInformation', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
        ]);
        User::partner()
            ->findOrFail($id)
            ->update($validated);

        return $this->redirectBackSuccess(__('Partner user updated'));
    }

    public function performanceIndex(Request $request)
    {
        if (Gate::denies('viewAny-' . AppUserSource::get('admin'). '-partners-performance-viewAny')) {
            abort(403);
        }
        return Inertia::render('Admin/PartnersPerformance', [
            'page_title' => __('Partners performance'),
            'header' => __('Partners performance'),
        ]);
    }

    // public function loginAs(Request $request,  \Illuminate\Contracts\Auth\StatefulGuard $guard)
    public function loginAsPartner(Request $request)
    {
        if (Gate::denies('loginAs-' . AppUserSource::get('admin'). '-partner-management-loginAs')) {
            abort(403);
        }
        // note auth() \Illuminate\Auth\AuthManager has 2 guards:
        //  sanctum \Illuminate\Auth\RequestGuard. This is default guard: Auth::getDefaultDriver() 
        //  web \Illuminate\Auth\SessionGuard
        // Both guards have resolved user instance. RequestGuard does not have any login() methods, and will return resolved $this->user.
        // When we auth new user via SessionGuard, RequestGuard will still have previous user instance and this will prevent the login on redirect,
        // however we can call any method on AuthManager default driver instance. RequestGuard use GuardHelpers trait and there we can call setUser($user) method:
        
        session()->put('auth_fittr_user', $request->user()->id);
        $user = auth()->guard('web')->loginUsingId($request->id);
        auth()->guard()->setUser($user);
        $url = route($user?->dashboard_route);

        return Inertia::location($url);
    }

    // public function loginBack(Request $request)
    // {
        // we can add a method here to log fittr admin back from partner. May not need this.
    // }
}
