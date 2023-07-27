<?php

namespace App\Http\Controllers\Partner;

use App\Enums\PartnerUserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\MemberFormRequest;
use App\Models\Partner\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class PartnerMemberController extends Controller
{
    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Partner/Member/Index', [
            'members' => User::member()
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
                    //may inject number of classes run/scheduled ?
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Members'),
            'header' => __('Members'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Partner/Member/Create', [
            'page_title' => __('Create Member'),
            'header' => array(
                [
                    'title' => __('Members'),
                    'link' => route('partner.members.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Create new member'),
                    'link' => null,
                ],
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MemberFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MemberFormRequest $request)
    {
        $fields = array_merge(
            $request->validated(),
            [
                'role' => PartnerUserRole::MEMBER->value,
                'password' => Hash::make('password'),
            ]
        );
        User::create($fields);

        return $this->redirectBackSuccess(__('Member created successfully'), 'partner.members.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\User  $member
     * @return \Illuminate\Http\Response
     */
    public function show(User $member)
    {
        return Inertia::render('Partner/Member/Show', [
            'page_title' => __('Member details'),
            'header' => array(
                [
                    'title' => __('Members'),
                    'link' => route('partner.members.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Member details'),
                    'link' => null,
                ],
            ),
            'member' => $member,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Partner\User  $member
     * @return \Illuminate\Http\Response
     */
    public function edit(User $member)
    {
        return Inertia::render('Partner/Member/Edit', [
            'page_title' => __('Edit Member'),
            'header' => array(
                [
                    'title' => __('Members'),
                    'link' => route('partner.members.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit Member'),
                    'link' => null,
                ],
            ),
            'member' => $member
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\MemberFormRequest  $request
     * @param  \App\Models\Partner\User  $member
     * @return \Illuminate\Http\Response
     */
    public function update(MemberFormRequest $request, User $member)
    {
        $member->update($request->validated());

        return $this->redirectBackSuccess(__('Member updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\User  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $member)
    {
        $member->delete();

        return $this->redirectBackSuccess(__('Member deleted successfully'), 'partner.members.index');
    }
}
