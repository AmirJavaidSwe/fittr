<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\ClassType;
use App\Models\Partner\Pack;
use App\Models\Partner\Location;
use App\Services\Store\StorePackService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Inertia\Inertia;

class StorePackController extends Controller
{
    public function __construct(StorePackService $store_pack_service)
    {
        $this->store_pack_service = $store_pack_service;
    }

    public function index(Request $request)
    {
        $packs = $this->store_pack_service->activePacks();
        $price_buttons = $this->store_pack_service->priceButtons($packs->pluck('prices')->flatten());

        return Inertia::render('Store/Packs/Index', [
            'page_title' => __('Memberships'),
            'header' => __('Memberships'),
            'packs' => $packs,
            'price_buttons' => $price_buttons,
            'price_buttons_text' => $this->store_pack_service->buttonsText(),
            'locations' => Location::active()->select('id', 'title')->get(),
            'classtypes' => ClassType::orderBy('id', 'desc')->select('title', 'id')->get(),
        ]);
    }

    public function showPrivate(Request $request)
    {
        $pack = Pack::active()->with(['prices' => function (Builder $query) {
                $query->where('is_active', true);
            }, 'prices.locations:id,title,status'
        ])
        ->where('private_url', $request->url)
        ->firstOrFail();

        $price_buttons = $this->store_pack_service->priceButtons($pack->prices);

        return Inertia::render('Store/Packs/ShowPrivate', [
            'page_title' => __('Memberships'),
            'header' => __('Private Memberships'),
            'pack' => $pack,
            'price_buttons' => $price_buttons,
            'price_buttons_text' => $this->store_pack_service->buttonsText(),
            'locations' => Location::active()->select('id', 'title')->get(),
            'classtypes' => ClassType::orderBy('id', 'desc')->select('title', 'id')->get(),
        ]);
    }
}
