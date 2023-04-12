<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\ClasstypeFormRequest;
use App\Models\Partner\Classtype;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerClassTypeController extends Controller
{
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

        return Inertia::render('Partner/Classtype/Index', [
            'classtypes' => Classtype::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('title', 'LIKE', '%'.$this->search.'%')
                              ->orWhere('description', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Settings - Class Types'),
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
                    'title' => __('Class Types'),
                    'link' => null,
                ],
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Partner/Classtype/Create', [
            'page_title' => __('Create Class Type'),
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
                    'title' => __('Class Types'),
                    'link' => route('partner.classtypes.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Create new'),
                    'link' => null,
                ],
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Partner\ClasstypeFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClasstypeFormRequest $request)
    {
        Classtype::create($request->validated());

        return $this->redirectBackSuccess(__('Class Type created successfully'), 'partner.classtypes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\Classtype  $classtype
     * @return \Illuminate\Http\Response
     */
    public function show(Classtype $classtype)
    {
        return Inertia::render('Partner/Classtype/Show', [
            'page_title' => __('Class Type details'),
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
                    'title' => __('Class Types'),
                    'link' => route('partner.classtypes.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Class Type details'),
                    'link' => null,
                ],
            ),
            'classtype' => $classtype,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Partner\Classtype  $classtype
     * @return \Illuminate\Http\Response
     */
    public function edit(Classtype $classtype)
    {
        return Inertia::render('Partner/Classtype/Edit', [
            'page_title' => __('Edit Class Type'),
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
                    'title' => __('Class Types'),
                    'link' => route('partner.classtypes.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit Class Type'),
                    'link' => null,
                ],
            ),
            'classtype' => $classtype
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Partner\ClasstypeFormRequest  $request
     * @param  \App\Models\Partner\Classtype  $classtype
     * @return \Illuminate\Http\Response
     */
    public function update(ClasstypeFormRequest $request, Classtype $classtype)
    {
        $classtype->update($request->validated());

        return $this->redirectBackSuccess(__('Class Type updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\Classtype  $classtype
     * @return \Illuminate\Http\Response
     */
    public function destroy(Classtype $classtype)
    {
        $classtype->delete();

        return $this->redirectBackSuccess(__('Class Type deleted successfully'), 'partner.classtypes.index');
    }
}
