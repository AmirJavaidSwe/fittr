<?php

namespace App\Http\Controllers\Partner;

use App\Enums\PackType;
use App\Enums\StripePeriod;
use App\Enums\StripePriceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\PackFormRequest;
use App\Http\Requests\Partner\PriceFormRequest;
use App\Http\Requests\Partner\PriceUpdateRequest;
use App\Models\Partner\Pack;
use App\Models\Partner\ClassType;
use App\Models\Partner\ServiceType;
use App\Models\Partner\Location;
use App\Models\Partner\PackPrice;
use App\Services\Partner\PackService;
use App\Services\Shared\Stripe\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PartnerPackController extends Controller
{

    public function __construct(PackService $pack_service, ProductService $stripe_product_service)
    {
        $this->middleware('business.ready');
        $this->pack_service = $pack_service;
        $this->stripe_product_service = $stripe_product_service;
    }

    public function __get($name)
    {
        if($name == 'connected_account_id') {
            return !empty($this->connected_account_id) ? $this->connected_account_id : $this->business()->stripe_account_id;
        }
        if($name == 'business_settings') {
            return !empty($this->business_settings) ? $this->business_settings : $this->business_settings();
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'type');
        $this->order_dir = $request->query('order_dir', 'asc');

        return Inertia::render('Partner/Pack/Index', [
            'packs' => Pack::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('title', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->with(['prices.locations'])
                ->paginate($this->per_page)
                ->withQueryString(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Settings - Packs'),
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
                    'title' => __('Membership packs'),
                    'link' => route('partner.packs.index'),
                ],
            ),
            'pack_types' => PackType::labels(),
            'price_types' => StripePriceType::labels(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Partner/Pack/Create', [
            'page_title' => __('Create Pack'),
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
                    'title' => __('Membership packs'),
                    'link' => route('partner.packs.index'),
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
            'pack_types' => PackType::labels(),
            'periods' => StripePeriod::labels(),
            'classtypes' => ClassType::orderBy('id', 'desc')->pluck('title', 'id'), //used for restrictions, with type Class+Hybrid
            'servicetypes' => ServiceType::orderBy('id', 'desc')->pluck('title', 'id'),//used for restrictions, with type Service+Hybrid
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Partner\PackFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PackFormRequest $request)
    {
        $pack = Pack::create($request->validated());
        $result = $this->stripe_product_service->createOrUpdatePackProduct($this->connected_account_id, $pack);

        return $result->error ? $this->redirectBackError($result->error_message) : $this->redirectBackSuccess(__('Pack created successfully'), 'partner.packs.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Partner\Pack  $pack
     * @return \Illuminate\Http\Response
     */
    public function show(Pack $pack)
    {
        return Inertia::render('Partner/Pack/Show', [
            'page_title' => __('Pack details'),
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
                    'title' => __('Membership packs'),
                    'link' => route('partner.packs.index'),
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
            'pack' => $pack->load(['prices']),
            'pack_types' => PackType::labels(),
            'periods' => StripePeriod::labels(),
            'price_types' => StripePriceType::labels(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Partner\Pack  $pack
     * @return \Illuminate\Http\Response
     */
    public function edit(Pack $pack)
    {
        $pack->load(['prices.locations'])->prices->transform(function ($item) {
            $item->location_ids = $item->locations->pluck('id');

            return $item;
        });

        return Inertia::render('Partner/Pack/Edit', [
            'page_title' => __('Edit Pack'),
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
                    'title' => __('Membership packs'),
                    'link' => route('partner.packs.index'),
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
            'pack' => $pack,
            'pack_types' => PackType::labelsForExisting($pack->type),
            'price_types' => StripePriceType::labels(),
            'periods' => StripePeriod::labels(),
            'classtypes' => ClassType::orderBy('id', 'desc')->pluck('title', 'id'),
            'servicetypes' => ServiceType::orderBy('id', 'desc')->pluck('title', 'id'),
            'locations' => Location::select('id', 'status', 'title')->get()->map(function ($item) {
                return array(
                    'label' => $item->title,
                    'value' => $item->id,
                    'status' => $item->status,
                );
            }),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Partner\PackFormRequest  $request
     * @param  \App\Models\Partner\Pack  $pack
     * @return \Illuminate\Http\Response
     */
    public function update(PackFormRequest $request, Pack $pack)
    {
        if(!$this->pack_service->canChangeType($request->type, $pack->type)){
            return $this->redirectBackError(__('Pack type can not be changed.'));
        }

        //TODO Add method to normalize validated fields, e.g. if type is default, is_restricted = false, etc

        $pack->update($request->validated());
        $result = $this->stripe_product_service->createOrUpdatePackProduct($this->connected_account_id, $pack);

        return $result->error ? $this->redirectBackError($result->error_message) : $this->redirectBackSuccess(__('Pack updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\Pack  $pack
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pack $pack)
    {
        $pack->prices()->update(['is_active' => false]);
        $pack->delete();

        //archive product at stripe
        if(!empty($pack->stripe_product_id)){
            $this->stripe_product_service->updateProduct($this->connected_account_id, $pack->stripe_product_id, ['active' => false, 'metadata' => ['deleted_at'=> now()] ]);
        }

        return $this->redirectBackSuccess(__('Pack deleted successfully'), 'partner.packs.index');
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\Models\Partner\Pack  $pack
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Pack $pack)
    {
        $duplicate = $pack->replicate()->fill([
            'title' => Str::limit($pack->title, 240).' - duplicated',
        ]);
        $duplicate->stripe_product_id = null; //unset product id
        $duplicate->save();

        $result = $this->stripe_product_service->createOrUpdatePackProduct($this->connected_account_id, $duplicate);

        return $result->error ? $this->redirectBackError($result->error_message) : $this->redirectBackSuccess(__('Pack duplicated successfully'), 'partner.packs.index');
    }

    /**
     * Create new Price for Pack.
     *
     * @param  \App\Models\Partner\Pack  $pack
     * @param  \App\Models\Partner\PackPrice  $price
     * @return \Illuminate\Http\Response
     */
    public function storePrice(Pack $pack, PriceFormRequest $request)
    {
        if(empty($pack->stripe_product_id)){
            return $this->redirectBackError(__('Please, save the pack and then try again.'));
        }

        $params = array(
            'pack' => $pack,
            'validated_data' => $request->validated(),
            'currency' => $this->business_settings['default_currency'] ?? null,
            'currency_symbol' => $this->business_settings['default_currency_symbol'] ?? null,
            'connected_account_id' => $this->connected_account_id,
        );
        $result = $this->stripe_product_service->createPackPrice($params);

        return $result->error ? $this->redirectBackError($result->error_message) : $this->redirectBackSuccess(__('Pricing option created successfully'));
    }

    /**
     * Update existing Price for Pack.
     *
     * @param  \App\Models\Partner\PackPrice  $price
     * @return \Illuminate\Http\Response
     */
    public function updatePrice(PackPrice $price, PriceUpdateRequest $request)
    {
        // this method handles price update and delete.
        // update API call can only change 'active' parameter
        // delete method can only delete the price locally, API call is the same but 'active' param is false, so that price can be archived only

        switch ($request->action) {
            case 'edit': //only local fields (no api call) excluding: type, status, price, interval...
                $price->locations()->sync($request->location_ids ?? []);
                $price->update($request->validated());
                $msg = __('Price updated successfully.');

                return $this->redirectBackSuccess($msg);
            case 'toggle':
                $active = !$price->is_active;
                $msg = $active ?  __('Pricing option enabled.') :  __('Pricing option disabled.');
                $price->update(['is_active' => $active]);
                $price_data = array(
                    'active' => $active,
                );
                break;
            case 'delete':
                $price->delete();
                $price_data = array(
                    'active' => false,
                    'metadata' => [
                        'deleted_at' => $price->deleted_at,
                    ],
                );
                $msg = __('Price updated deleted.');
                break;
            default:
                return $this->redirectBackError();
        }

        $result = $this->stripe_product_service->updatePrice($this->connected_account_id, $price->stripe_price_id, $price_data);

        return $result->error ? $this->redirectBackError($result->error_message) : $this->redirectBackSuccess(__($msg));
    }
}
