<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use URL;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Admin/Partners', [
            'users' => User::partner()
                ->orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('name', 'LIKE', '%'.$this->search.'%')
                              ->orWhere('email', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString()
                //AbstractPaginator@through(), transforms chunk
                ->through(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('Y-m-d'),
                    'url_show' => URL::route('admin.partners.show', $user),
                    'url_edit' => URL::route('admin.partners.edit', $user),
                ])
                ,
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
        $partner = User::partner()->findOrFail($id);

        return Inertia::render('Admin/PartnerShow', [
            'partner' => $partner,
            'page_title' => __('Partner details'),
            'header' => __('Partner details'),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $partner = User::partner()->findOrFail($id);

        return Inertia::render('Admin/PartnerEdit', [
            'partner' => $partner,
            'page_title' => __('Partner details'),
            'header' => __('Update partner details'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validateWithBag('updateProfileInformation', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
        ]);
        User::partner()->findOrFail($id)->update($validated);
        session()->flash('flash_type', 'success');
        session()->flash('flash_timestamp', time());
    }

    public function performanceIndex(Request $request)
    {
        return Inertia::render('Admin/PartnersPerformance', [
            'page_title' => __('Partners performance'),
            'header' => __('Partners performance'),
        ]);
    }
}
