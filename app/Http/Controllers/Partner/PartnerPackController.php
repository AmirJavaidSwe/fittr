<?php

namespace App\Http\Controllers\Partner;

use App\Enums\PackType;
use App\Enums\StripePeriod;
use App\Enums\StripePriceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\PackFormRequest;
use App\Http\Requests\Partner\PriceFormRequest;
use App\Models\Partner\Pack;
use App\Models\Partner\ClassType;
use App\Models\Partner\PackPrice;
use App\Services\Shared\StripeProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PartnerPackController extends Controller
{

    public function __construct(StripeProductService $stripe_product_service)
    {
        $this->middleware('business.ready');
        $this->stripe_product_service = $stripe_product_service;
    }

    public function __get($name)
    {
        if($name == 'connected_account_id') {
            return !empty($this->connected_account_id) ? $this->connected_account_id : $this->business()->stripe_account_id;
        }
        if($name == 'business_seetings') {
            return !empty($this->business_seetings) ? $this->business_seetings : $this->business_seetings();
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
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Partner/Pack/Index', [
            'packs' => Pack::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                              ->orWhere('title', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->with(['prices'])
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
                    'link' => null,
                ],
            ),
            'classtypes' => ClassType::orderBy('id', 'desc')->pluck('title', 'id'),
            'pack_types' => PackType::labels(),
            'price_types' => StripePriceType::labels(),
            'periods' => StripePeriod::labels(),
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
            //FUTURE USE: 'servicetypes' => ServiceType::orderBy('id', 'desc')->pluck('title', 'id'), //used for restrictions, with type Service+Hybrid

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
            'pack' => $pack->load(['prices']),
            'pack_types' => PackType::labels(),
            'price_types' => StripePriceType::labels(),
            'periods' => StripePeriod::labels(),
            'classtypes' => ClassType::orderBy('id', 'desc')->pluck('title', 'id'),
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
        //TODO Add method to normalize validated fields, e.g. if type is default, set sessions = 0, is_restricted = false, is_unlimited = false, etc

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
        $params = array(
            'pack' => $pack,
            'validated_data' => $request->validated(),
            'currency' => $this->business_seetings['default_currency'] ?? 'gbp',
            'connected_account_id' => $this->connected_account_id,
        );
        $result = $this->stripe_product_service->createPackPrice($params);

        return $result->error ? $this->redirectBackError($result->error_message) : $this->redirectBackSuccess(__('Pricing option created successfully'));
    }

    /**
     * Update existing Price for Pack.
     *
     * @param  \App\Models\Partner\Pack  $pack
     * @param  \App\Models\Partner\PackPrice  $price
     * @return \Illuminate\Http\Response
     */
    public function updatePrice(Pack $pack, PackPrice $price, Request $request)
    {
        // this method handles price update and delete.
        // update API cal can only change 'active' parameter
        // delete method can only delete the price locally, API call is the same but 'active' param is false, so that price can be archived only
        $active = false;       
        $msg = 'Pricing option deleted.';
        switch ($request->action) {
            case 'toggle':
                $active = !$price->is_active;
                $msg = 'Pricing option updated.';
                $price->update(['is_active' => $active]);
                $price_data = array(
                    'active' => $active,
                );
                break;
            case 'delete':
                $price->delete();
                $price_data = array(
                    'active' => $active,
                    'metadata' => [
                        'deleted_at' => $price->deleted_at,
                    ],
                );
                break;
        }

        $result = $this->stripe_product_service->updatePrice($this->connected_account_id, $price->stripe_price_id, $price_data);

        return $result->error ? $this->redirectBackError($result->error_message) : $this->redirectBackSuccess(__($msg));
    }
}
