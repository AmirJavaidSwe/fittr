<?php

namespace App\Http\Controllers\Partner;

use App\Enums\StateType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\ServiceTypeFormRequest;
use App\Models\Partner\ServiceType;
use App\Traits\ImageableTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PartnerServiceTypeController extends Controller
{
    use ImageableTrait;

    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;
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
        $this->order_dir = $request->query('order_dir', 'asc');

        return Inertia::render('Partner/Servicetype/Index', [
            'servicetypes' => ServiceType::orderBy($this->order_by, $this->order_dir)
                ->with(['images'])
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
            'page_title' => __('Settings - Service Types'),
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
                    'title' => __('Service Types'),
                    'link' => route('partner.servicetypes.index'),
                ],
            ),
            'statuses' => StateType::labels(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return Inertia::render('Partner/Servicetype/Create', [
            'page_title' => __('Create Service Type'),
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
                    'title' => __('Service Types'),
                    'link' => route('partner.servicetypes.index'),
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
            'statuses' => StateType::labels(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Partner\ServiceTypeFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceTypeFormRequest $request)
    {
        $service_type = ServiceType::create($request->validated());

        try {
            //upload new and/or delete existing
            if($request->hasFile('image')) {
                $this->updateFiles($request->file('image'), [], $service_type, session('business.id').'/service_types');
            }
        } catch(\Exception $e) {
            logger()->error($e->getMessage());
        }

        if(request()->has('returnTo')) {
            return redirect()->route(request()->returnTo);
        }

        return $this->redirectBackSuccess(__('Service type created successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\ServiceType  $servicetype
     * @return Response
     */
    public function show(ServiceType $servicetype)
    {
        return Inertia::render('Partner/Servicetype/Show', [
            'page_title' => __('Service Type details'),
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
                    'title' => __('Service Types'),
                    'link' => route('partner.servicetypes.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Details'),
                    'link' => null,
                ],
            ),
            'servicetype' => $servicetype->load(['images']),
            'statuses' => StateType::labels(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Partner\ServiceType  $servicetype
     * @return Response
     */
    public function edit(ServiceType $servicetype)
    {
        return Inertia::render('Partner/Servicetype/Edit', [
            'page_title' => __('Edit Service Type'),
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
                    'title' => __('Service Types'),
                    'link' => route('partner.servicetypes.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit'),
                    'link' => null,
                ],
            ),
            'servicetype' => $servicetype->load(['images']),
            'statuses' => StateType::labels(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Partner\ServiceTypeFormRequest  $request
     * @param  \App\Models\Partner\ServiceType  $servicetype
     * @return Response
     */
    public function update(ServiceTypeFormRequest $request, ServiceType $servicetype)
    {
        $servicetype->update($request->validated());

        try {
            //upload new and/or delete existing
            if($request->hasFile('image') || !$request->old_image) {
                $this->updateFiles($request->file('image'), [], $servicetype, session('business.id').'/service_types');
            }
        } catch(\Exception $e) {
            logger()->error($e->getMessage());
            return $this->redirectBackError(__('Unable processing image.'));
        }

        return $this->redirectBackSuccess(__('Service type updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\ServiceType  $servicetype
     * @return Response
     */
    public function destroy(ServiceType $servicetype)
    {
        $servicetype->delete();

        return $this->redirectBackSuccess(__('Service type deleted successfully'));
    }
}
