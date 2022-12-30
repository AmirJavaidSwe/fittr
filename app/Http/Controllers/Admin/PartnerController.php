<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use URL;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);

        return Inertia::render('Admin/Partners', [
            'users' => User::partner()
                ->orderBy('name')
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('name', 'LIKE', '%'.$this->search.'%')
                              ->orWhere('email', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString()
                //AbstractPaginator@through(), trnasforms chunk
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
            'page_title' => __('Partners'),
            'header' => __('Partners management'),
        ]);
    }

    public function show(Request $request, $id)
    {
        $parner = User::partner()->find($id);

        return Inertia::render('Admin/PartnerShow', [
            'parner' =>  [
                'id' => $parner->id,
                'name' => $parner->name,
                'email' => $parner->email,
                'created_at' => $parner->created_at->format('Y-m-d'),
                'show_url' => URL::route('admin.partners.show', $parner),
                'edit_url' => URL::route('admin.partners.edit', $parner),
            ],
            'page_title' => __('Partners'),
            'header' => __('Partner details'),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $parner = User::partner()->find($id);

        return Inertia::render('Admin/PartnerEdit', [
            'parner' =>  [
                'id' => $parner->id,
                'name' => $parner->name,
                'email' => $parner->email,
                'created_at' => $parner->created_at->format('Y-m-d'),
                'show_url' => URL::route('admin.partners.show', $parner),
                'edit_url' => URL::route('admin.partners.edit', $parner),
            ],
            'page_title' => __('Partners'),
            'header' => __('Update partner details'),
        ]);
    }

    public function performanceIndex(Request $request)
    {
        return Inertia::render('Admin/PartnersPerformance', [
            'page_title' => __('Partners performance'),
            'header' => __('Partners performance'),
        ]);
    }
}
