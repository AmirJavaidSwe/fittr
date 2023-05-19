<?php

namespace App\Http\Controllers\Admin;

use URL;
use App\Models\User;
use Inertia\Inertia;
use App\Enums\AppUserSource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PartnerController extends Controller
{
    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;

    public function index(Request $request)
    {
        if (Gate::denies('viewAny-' . AppUserSource::admin->name . '-partner-management-viewAny')) {
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
                            ->orWhere('name', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('email', 'LIKE', '%' . $this->search . '%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString()
                //AbstractPaginator@through(), transforms chunk
                ->through(
                    fn ($user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'business_id' => $user->business_id,
                        'business_name' => $user->business->settings->firstWhere('key', 'business_name')?->val,
                        'created_at' => $user->created_at->format('Y-m-d'),
                        'url_show' => URL::route('admin.partners.show', $user),
                        'url_edit' => URL::route('admin.partners.edit', $user),
                        'url_login_as' => URL::route('admin.partners.login-as', $user),
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
        if (Gate::denies('view-' . AppUserSource::admin->name . '-partner-management-view')) {
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
        if (Gate::denies('update-' . AppUserSource::admin->name . '-partner-management-update')) {
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
        if (Gate::denies('update-' . AppUserSource::admin->name . '-partner-management-update')) {
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
        if (Gate::denies('viewAny-' . AppUserSource::admin->name . '-partners-performance-viewAny')) {
            abort(403);
        }
        return Inertia::render('Admin/PartnersPerformance', [
            'page_title' => __('Partners performance'),
            'header' => __('Partners performance'),
        ]);
    }

    public function loginAs(Request $request, $id)
    {
        if (Gate::denies('loginAs-' . AppUserSource::admin->name . '-partner-management-loginAs')) {
            abort(403);
        }
        $user = User::partner()->findOrFail($id);

        if (Auth::guard('web')->id() != $id) {
            if (!session()->has('orig_user')) {
                session()->put('orig_user', Auth::guard('web')->id());
            }

            Auth::guard('web')->loginUsingId($id);

            session()->put('user_switch_url', url()->previous());
        }

        return redirect()->route('partner.classes.index');
    }

    public function switchBack(Request $request)
    {
        if (session()->has('orig_user')) {
            Auth::guard('web')->loginUsingId(session()->get('orig_user'));
            session()->forget('orig_user');
        }

        return redirect(session()->get('user_switch_url', '/'));
    }
}
