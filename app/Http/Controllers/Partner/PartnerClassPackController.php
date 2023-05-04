<?php

namespace App\Http\Controllers\Partner;

use App\Enums\ClasspackType;
use App\Enums\ClasspackExpirationPeriod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\ClasspackFormRequest;
use App\Models\Partner\Classpack;
use App\Models\Partner\Classtype;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerClassPackController extends Controller
{
    // public function __construct()
    // {
        
    // }

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

        return Inertia::render('Partner/Classpack/Index', [
            'classpacks' => Classpack::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('title', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Settings - Class Packs'),
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
                    'title' => __('Class Packs'),
                    'link' => null,
                ],
            ),
            'options_types' => ClasspackType::labels(),
            'options_periods' => ClasspackExpirationPeriod::labels(),
            'classtypes' => Classtype::orderBy('id', 'desc')->pluck('title', 'id'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Partner/Classpack/Create', [
            'page_title' => __('Create Class Pack'),
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
                    'title' => __('Class Packs'),
                    'link' => route('partner.classpacks.index'),
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
            'options_types' => ClasspackType::labels(),
            'options_periods' => ClasspackExpirationPeriod::labels(),
            'classtypes' => Classtype::orderBy('id', 'desc')->pluck('title', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Partner\ClasspackFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClasspackFormRequest $request)
    {
        Classpack::create($request->validated());

        return $this->redirectBackSuccess(__('Class Pack created successfully'), 'partner.classpacks.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\Classpack  $classpack
     * @return \Illuminate\Http\Response
     */
    public function show(Classpack $classpack)
    {
        return Inertia::render('Partner/Classpack/Show', [
            'page_title' => __('Class Pack details'),
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
                    'title' => __('Class Packs'),
                    'link' => route('partner.classpacks.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Class Pack details'),
                    'link' => null,
                ],
            ),
            'classpack' => $classpack,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Partner\Classpack  $classtype
     * @return \Illuminate\Http\Response
     */
    public function edit(Classpack $classpack)
    {
        return Inertia::render('Partner/Classpack/Edit', [
            'page_title' => __('Edit Class Pack'),
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
                    'title' => __('Class Packs'),
                    'link' => route('partner.classpacks.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit Class Pack'),
                    'link' => null,
                ],
            ),
            'classpack' => $classpack,
            'options_types' => ClasspackType::labels(),
            'options_periods' => ClasspackExpirationPeriod::labels(),
            'classtypes' => Classtype::orderBy('id', 'desc')->pluck('title', 'id'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Partner\ClasspackFormRequest  $request
     * @param  \App\Models\Partner\Classpack  $classpack
     * @return \Illuminate\Http\Response
     */
    public function update(ClasspackFormRequest $request, Classpack $classpack)
    {
        $classpack->update($request->validated());

        return $this->redirectBackSuccess(__('Class Pack updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\Classpack  $classpack
     * @return \Illuminate\Http\Response
     */
    public function destroy(Classpack $classpack)
    {
        $classpack->delete();

        return $this->redirectBackSuccess(__('Class Pack deleted successfully'), 'partner.classpacks.index');
    }
}
