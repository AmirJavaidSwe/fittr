<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\AmenityRequest;
use App\Models\Partner\Amenity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PartnerAmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|Response
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Partner/Amenity/Index', [
            'amenities' => Amenity::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('title', 'LIKE', '%'.$this->search.'%')
                              ->orWhere('contents', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Settings - Amenities'),
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
                    'title' => __('Amenities'),
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
        return Inertia::render('Partner/Amenity/Create', [
            'page_title' => __('Create Amenity'),
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
                    'title' => __('Amenities'),
                    'link' => route('partner.amenity.index'),
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
     * @param AmenityRequest $request
     * @return RedirectResponse
     */
    public function store(AmenityRequest $request)
    {
        Amenity::create($request->validated());

        return $this->redirectBackSuccess(__('Amenity created successfully'), 'partner.amenity.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Amenity $amenity
     * @return Response
     */
    public function show(Amenity $amenity)
    {
        // image path provided by model accessor
        return Inertia::render('Partner/Amenity/Show', [
            'page_title' => __('Amenity details'),
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
                    'title' => __('Amenities'),
                    'link' => route('partner.amenity.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Amenity details'),
                    'link' => null,
                ],
            ),
            'amenity' => $amenity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Amenity $amenity
     * @return Response
     */
    public function edit(Amenity $amenity)
    {
        return Inertia::render('Partner/Amenity/Edit', [
            'page_title' => __('Edit Amenity'),
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
                    'title' => __('Amenities'),
                    'link' => route('partner.amenity.index'),
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
            'amenity' => $amenity,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AmenityRequest $request
     * @param Amenity $amenity
     * @return RedirectResponse
     */
    public function update(AmenityRequest $request, Amenity $amenity)
    {
        $amenity->update($request->all());

        return $this->redirectBackSuccess(__('Amenity updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Amenity $amenity
     * @return RedirectResponse
     */
    public function destroy(Amenity $amenity)
    {
        $amenity->delete();

        return $this->redirectBackSuccess(__('Amenity deleted successfully'), 'partner.amenity.index');
    }
}
