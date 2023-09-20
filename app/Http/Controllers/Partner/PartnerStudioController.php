<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StudioFormRequest;
use App\Models\Partner\ClassType;
use App\Models\Partner\Location;
use App\Models\Partner\Studio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerStudioController extends Controller
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
        $this->order_dir = $request->query('order_dir', 'asc');

        return Inertia::render('Partner/Studio/Index', [
            'studios' => Studio::with('location', 'class_type_studios')->orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('title', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'locations' => Location::select('id', 'title')->get(),
            'class_types' => ClassType::select('id as value', 'title as label')->get(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Settings - Studios'),
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
                    'title' => __('Studios'),
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
        return Inertia::render('Partner/Studio/Create', [
            'page_title' => __('Create Studio'),
            'locations' => Location::select('id', 'title')->get(),
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
                    'title' => __('Studios'),
                    'link' => route('partner.studios.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Create new studio'),
                    'link' => null,
                ],
            ),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Partner\StudioFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StudioFormRequest $request)
    {
        $studio = Studio::create($request->validated());

        if(request()->has('returnTo')) {
            $extra = array('studio' => $studio);
            return redirect()->route(request()->returnTo)->with('extra', $extra);
        }

        return $this->redirectBackSuccess(__('Studio created successfully'), 'partner.studios.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\Studio  $studio
     * @return \Illuminate\Http\Response
     */
    public function show(Studio $studio)
    {
        return Inertia::render('Partner/Studio/Show', [
            'page_title' => __('Studio details'),
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
                    'title' => __('Studios'),
                    'link' => route('partner.studios.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Studio details'),
                    'link' => null,
                ],
            ),
            'studio' => $studio->load('location'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Partner\Studio  $studio
     * @return \Illuminate\Http\Response
     */
    public function edit(Studio $studio)
    {
        return Inertia::render('Partner/Studio/Edit', [
            'page_title' => __('Edit Studio'),
            'locations' => Location::select('id', 'title')->get(),
            'class_types' => ClassType::select('id as value', 'title as label')->get(),
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
                    'title' => __('Studios'),
                    'link' => route('partner.studios.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit Studio'),
                    'link' => null,
                ],
            ),
            'studio' => $studio->load('class_type_studios')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Partner\StudioFormRequest  $request
     * @param  \App\Models\Partner\Studio  $studio
     * @return \Illuminate\Http\Response
     */
    public function update(StudioFormRequest $request, Studio $studio)
    {
        $validated = $request->validated();
        $classTypeStudios = collect($validated['class_type_studios'] ?? []);
        $studio->update($validated);

        $ids = $studio->class_type_studios->pluck('id');
        $updatedIds = $classTypeStudios->pluck('id');
        $diff = $ids->diff($updatedIds);

        if($diff->count()) {
            $studio->class_type_studios()->whereIn('id', $diff)->delete();
        }

        if($classTypeStudios->where(fn ($item) => !@$item['id'])->count()) {
            $studio->class_type_studios()->createMany($classTypeStudios->where(fn ($item) => !@$item['id']));
        }

        if($classTypeStudios->where(fn ($item) => @$item['id'])->count()) {
            $classTypeStudios->where(fn ($item) => @$item['id'])->each(function ($item) use($studio) {
                $studio->class_type_studios()->whereId(@$item['id'])->update($item);
            });
        }

        return $this->redirectBackSuccess(__('Studio updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\Studio  $studio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Studio $studio)
    {
        $studio->delete();

        return $this->redirectBackSuccess(__('Studio deleted successfully'), 'partner.studios.index');
    }
}
