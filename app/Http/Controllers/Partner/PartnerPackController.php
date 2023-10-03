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
        return Inertia::render('Partner/Pack/Index', [
            'packs' =>  $this->pack_service->getGroupedPacks(),
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
        $data = $this->pack_service->normalizedPack($request->validated());
        $pack = Pack::create($data);
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
            'pack' => $pack->load(['pack_prices']),
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
        $pack->load(['pack_prices.locations'])->pack_prices->transform(function ($item) {
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

        $data = $this->pack_service->normalizedPack($request->validated());
        $pack->update($data);
        $result = $this->stripe_product_service->createOrUpdatePackProduct($this->connected_account_id, $pack);

        return $result->error ? $this->redirectBackError($result->error_message) : $this->redirectBackSuccess(__('Pack updated successfully'));
    }

    public function toggle(Pack $pack)
    {
        $active = !$pack->is_active;
        $msg = $active ?  __('Pack enabled.') :  __('Pack disabled.');
        $pack->update(['is_active' => $active]);

        return $this->redirectBackSuccess($msg);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Partner\Pack  $pack
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pack $pack)
    {
        $pack->pack_prices()->update(['is_active' => false]);
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
     * Sort the packs.
     *
     * @param  \App\Models\Partner\Pack  $pack
     * @return \Illuminate\Http\Response
     */
    public function sortPacks(Request $request)
    {
        $data = collect($request->all())->keyBy('id');
        $packs = Pack::whereIn('id', $data->keys())->each(function($pack) use ($data) {
            $sort = $data[$pack->id] ?? null;
            if(!empty($sort)){
                $pack->update(['ordering' => $sort['ordering']]);
            };
        });

        return response()->json([
            'data' => $request->all(),
            'success' => $packs, //bool
        ]);
    }

    /**
     * Sort the pack prices.
     *
     * @param  \App\Models\Partner\Pack  $pack
     * @return \Illuminate\Http\Response
     */
    public function sortPackPrices(Request $request)
    {
        $data = collect($request->all())->keyBy('id');
        $pack_prices = PackPrice::whereIn('id', $data->keys())->each(function($pack_price) use ($data) {
            $sort = $data[$pack_price->id] ?? null;
            if(!empty($sort)){
                $pack_price->update(['ordering' => $sort['ordering']]);
            }
        });

        return response()->json([
            'data' => $request->all(),
            'success' => $pack_prices, //bool
        ]);
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

        $data = $this->pack_service->normalizedPrice($pack, $request->validated());

        $params = array(
            'pack' => $pack,
            'validated_data' => $data,
            'currency' => $this->business_settings['default_currency'] ?? null,
            'currency_symbol' => $this->business_settings['default_currency_symbol'] ?? null,
            'connected_account_id' => $this->connected_account_id,
        );
        $stripe_price = $this->stripe_product_service->createPackPrice($params);
        if($stripe_price->error){
            return $this->redirectBackError($stripe_price->error_message);
        }

        $model_data = [
            'stripe_price_id' => $stripe_price->data?->id,
            'unit_amount' => $data['price'] * 100,
            'currency' => $params['currency'],
            'currency_symbol' => $params['currency_symbol'],
        ] + $data;
        //create new price
        $price = $pack->pack_prices()->create($model_data);
        //sync location restrictions
        $price->locations()->sync($data['location_ids'] ?? []);

        return $this->redirectBackSuccess(__('Pricing option created successfully'));
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
        $pack = $price->priceable;
        $data = $this->pack_service->normalizedPrice($pack, $request->validated());

        switch ($request->action) {
            case 'edit': //only local fields (no api call) excluding: type, status, price, interval...
                $price->locations()->sync($data['location_ids'] ?? []);
                $price->update($data);
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
                $msg = __('Price option deleted.');
                break;
            default:
                return $this->redirectBackError();
        }

        $result = $this->stripe_product_service->updatePrice($this->connected_account_id, $price->stripe_price_id, $price_data);

        return $result->error ? $this->redirectBackError($result->error_message) : $this->redirectBackSuccess(__($msg));
    }
}
