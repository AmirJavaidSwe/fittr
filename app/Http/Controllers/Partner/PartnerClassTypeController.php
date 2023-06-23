<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\ClasstypeFormRequest;
use App\Models\Partner\ClassType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PartnerClassTypeController extends Controller
{

    public $search ;
    public $per_page ;
    public $order_by ;
    public $order_dir ;
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Partner/Classtype/Index', [
            'classtypes' => ClassType::orderBy($this->order_by, $this->order_dir)
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
     * @return Response
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
        ClassType::create($request->validated());

        if(request()->has('returnTo')) {
            return redirect()->route(request()->returnTo);
        }

        return $this->redirectBackSuccess(__('Class Type created successfully'), 'partner.classtypes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\ClassType  $classtype
     * @return Response
     */
    public function show(ClassType $classtype)
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
     * @param  \App\Models\Partner\ClassType  $classtype
     * @return Response
     */
    public function edit(ClassType $classtype)
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
     * @param  \App\Models\Partner\ClassType  $classtype
     * @return Response
     */
    public function update(ClasstypeFormRequest $request, ClassType $classtype)
    {
        $classtype->update($request->validated());

        return $this->redirectBackSuccess(__('Class Type updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\ClassType  $classtype
     * @return Response
     */
    public function destroy(ClassType $classtype)
    {
        $classtype->delete();

        return $this->redirectBackSuccess(__('Class Type deleted successfully'), 'partner.classtypes.index');
    }
}
