<?php

namespace App\Http\Controllers\Partner;

use App\Models\Role;
use App\Models\User;
use App\Models\Country;
use App\Models\SystemModule;
use App\Models\Partner\Studio;
use App\Models\Partner\Amenity;
use App\Models\Partner\Location;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\LocationFormRequest;
use App\Traits\ImageableTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
     * @return Response
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'asc');

        return Inertia::render('Partner/Location/Index', [
            'locations' => Location::with('studios', 'manager', 'amenities', 'images')
                ->orderBy($this->order_by, $this->order_dir) // Need to figure out users.name sub query/join orderby as it is a cross db relation and location's connection 'mysql_partner' is not able to access main database.
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                        ->orWhere('title', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'users' => User::select('id', 'first_name', 'last_name', 'email')->partner()->where('business_id', auth()->user()->business_id)->get(),
            'countries' => Country::select('id', 'name')->whereStatus(1)->get(),
            'amenities' => Amenity::select('id', 'title')->get()->map(fn($item) => ['label' => $item->title, 'value' => $item->id]),
            'studios' => Studio::orphan()->select('id', 'title')->get()->map(fn($item) => ['value' => $item->id, 'label' => $item->title]),
            'roles' => Role::select('id', 'title')->where('source', auth()->user()->source)->where('business_id', auth()->user()->business_id)->get(),
            'systemModules' => SystemModule::with('permissions')->where('is_for', auth()->user()->source)->get(),
            'google_maps_key' => config('services.google_maps'),
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
                    'link' => route('partner.locations.index'),
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
        return Inertia::render('Partner/Location/Create', [
            'page_title' => __('Create Location'),
            'users' => User::select('id', 'first_name', 'last_name', 'email')->partner()->where('business_id', auth()->user()->business_id)->get(),
            'countries' => Country::select('id', 'name')->whereStatus(1)->get(),
            'amenities' => Amenity::select('id', 'title')->get()->map(fn($item) => ['label' => $item->title, 'value' => $item->id]),
            'google_maps_key' => config('services.google_maps'),
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
     * @return Response
     */
    public function store(LocationFormRequest $request)
    {
        $validated = $request->validated();
        $validated['page_title'] = $validated['title'];

        $location = Location::create($validated);

        if(!empty($validated['amenity_ids'])) {
            $location->amenities()->sync($validated['amenity_ids'] ?? []);
        }

        //list of studios contains orphans only
        if(!empty($validated['studio_ids'])) {
            Studio::whereNull('location_id')->whereIn('id', $validated['studio_ids'])->update(['location_id' => $location->id]);
        }

        try {
            $this->uploadFiles($request->file('image'), $location, session('business.id').'/locations');

            if(request()->has('returnTo')) {
                $extra = array('location' => $location);
                return redirect()->route(request()->returnTo)->with('extra', $extra);
            }
        } catch(\Exception $e) {
            if(request()->has('returnTo')) {
                return $this->redirectBackError(__('Something went wrong!'), request()->returnTo);
            }
            return $this->redirectBackError(__('Something went wrong!'), 'partner.locations.index');
        }
        return $this->redirectBackSuccess(__('Location created successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\Location  $location
     * @return Response
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
            'location' => $location->load(['manager', 'country', 'amenities', 'images', 'studios']),
            'google_maps_key' => config('services.google_maps'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Partner\Location  $location
     * @return Response
     */
    public function edit(Location $location)
    {
        return Inertia::render('Partner/Location/Edit', [
            'page_title' => __('Edit Location'),
            'users' => User::select('id', 'first_name', 'last_name', 'email')->partner()->where('business_id', auth()->user()->business_id)->get(),
            'countries' => Country::select('id', 'name')->whereStatus(1)->get(),
            'amenities' => Amenity::select('id', 'title')->get()->map(fn($item) => ['label' => $item->title, 'value' => $item->id]),
            'studios' => Studio::select('id', 'title')->get()->map(fn($item) => ['label' => $item->title, 'value' => $item->id]),
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
            'location' => $location->load('manager', 'country', 'amenities', 'images', 'studios'),
            'google_maps_key' => config('services.google_maps'),
        ]);
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
        $validated = $request->validated();

        $location->update($validated);

        $location->amenities()->sync($validated['amenity_ids'] ?? []);

        $savedStudios = $location->studios->pluck('id');
        $updatedStudios = collect($validated['studio_ids'] ?? []);
        $delStudios = $savedStudios->diff($updatedStudios);

        if($updatedStudios->count()) {
            Studio::whereIn('id', $updatedStudios)->update(['location_id' => $location->id]);
        }

        if($delStudios->count()) {
            Studio::whereIn('id', $delStudios)->update(['location_id' => null]);
        }

        try {
            if($request->hasFile('image')) {
                $this->updateFiles($request->file('image'), $request->uploaded_images, $location, session('business.id').'/locations');
            }

        } catch(\Exception $e) {
            return $this->redirectBackError(__('Something went wrong!'), 'partner.locations.index');
        }
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
        $location->images->each(function($image){
            $this->deleteImage($image);
        });
        Studio::whereIn('id', $location->studios->pluck('id'))->update(['location_id' => null]);
        $location->delete();

        return $this->redirectBackSuccess(__('Location deleted successfully'), 'partner.locations.index');
    }

    public function deleteImages(Request $request, Location $location)
    {
        $image = $location->images()->where('id', $request->image_id)->first();
        if($image){
            $this->deleteImage($image);
        }

        return $this->redirectBackSuccess(__('Image deleted successfully'));
    }
}
