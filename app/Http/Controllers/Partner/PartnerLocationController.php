<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\LocationFormRequest;
use App\Models\Country;
use App\Models\Partner\Amenity;
use App\Models\Partner\Location;
use App\Models\Partner\User;
use App\Traits\ImageableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PartnerLocationController extends Controller
{
    use ImageableTrait;

    protected $search;
    protected $per_page;
    protected $order_by;
    protected $order_dir;

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

        return Inertia::render('Partner/Location/Index', [
            'locations' => Location::with('manager', 'amenities', 'images')->orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('title', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'users' => User::select('id', 'name', 'email')->get(),
            'countries' => Country::select('id', 'name')->whereStatus(1)->get(),
            'amenities' => Amenity::select('id', 'title')->get()->map(fn($item) => ['label' => $item->title, 'value' => $item->id]),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Settings - Locations'),
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
                    'title' => __('Locations'),
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
        return Inertia::render('Partner/Location/Create', [
            'page_title' => __('Create Location'),
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
                    'title' => __('Locations'),
                    'link' => route('partner.locations.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Create new location'),
                    'link' => null,
                ],
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Partner\LocationFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationFormRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();

        try {

            $location = new Location;
            $location->title = $validated['title'];
            $location->page_title = $validated['title'];
            $location->brief = $validated['brief'];
            // $location->url = $validated['url'];
            // $location->checkin_url = $validated['checkin_url'];
            $location->manager_id = $validated['manager_id'];
            $location->address_line_1 = $validated['address_line_1'];
            $location->address_line_2 = $validated['address_line_2'];
            $location->country_id = $validated['country_id'];
            $location->city = $validated['city'];
            $location->postcode = $validated['postcode'];
            $location->map_latitude = $validated['map_latitude'];
            $location->map_longitude = $validated['map_longitude'];
            $location->tel = $validated['tel'];
            $location->email = $validated['email'];
            $location->save();

            $location->amenities()->sync($validated['amenity_ids']);

            $this->uploadFiles($request->file('image'), $location, 'images/location', 'public');

            DB::commit();

            return $this->redirectBackSuccess(__('Location created successfully'), 'partner.locations.index');

        } catch(Exception $e) {
            DB::rollBack();
            return $this->redirectBackError(__('Something went wrong!'), 'partner.locations.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {

        return Inertia::render('Partner/Location/Show', [
            'page_title' => __('Location details'),
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
                    'title' => __('Locations'),
                    'link' => route('partner.locations.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Location details'),
                    'link' => null,
                ],
            ),
            'location' => $location->load(['manager', 'country', 'amenities', 'images']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Partner\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        return redirect()->route('partner.locations.index')->with(['location' => $location]);
        /* return Inertia::render('Partner/Location/Edit', [
            'page_title' => __('Edit Location'),
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
                    'title' => __('Locations'),
                    'link' => route('partner.locations.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit Location'),
                    'link' => null,
                ],
            ),
            'location' => $location->load('manager', 'country')
        ]); */
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Partner\LocationFormRequest  $request
     * @param  \App\Models\Partner\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(LocationFormRequest $request, Location $location)
    {
        $location->update($request->validated());

        return $this->redirectBackSuccess(__('Location updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        $location->delete();

        return $this->redirectBackSuccess(__('Location deleted successfully'), 'partner.locations.index');
    }
}
