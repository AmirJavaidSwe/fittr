<?php

namespace App\Http\Controllers\Store;

use App\Enums\StripePriceType;
use App\Http\Controllers\Controller;
use App\Models\Partner\Pack;
use App\Models\Partner\Location;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Inertia\Inertia;

class StorePackController extends Controller
{
    public function index(Request $request)
    {
        $packs = Pack::active()->with(['prices' => function (Builder $query) {
            //keep only active prices
            $query->where('is_active', true);
        }, 'prices.locations:id,title,status'
        ])->get();

        $locations = Location::active()->select('id', 'title')->get();

        $price_buttons = $packs->pluck('prices')->flatten()->map(function ($item) {
            return array(
                'id' => $item->id,
                'pack_id' => $item->priceable_id,
                'type' => $item->type,
                'text' => StripePriceType::button($item->type),
                'locations' => $item->locations->pluck('id')->all(),
            );
        })
        ->groupBy('pack_id')
        ->map(function ($item) {
            $button_text = __('Select an option');
            $selected_price_id = null;
            $enabled = false;
            if($item->count() == 1 && empty($item->first()['locations'])){
                $button_text = $item->first()['text'];
                $selected_price_id = $item->first()['id'];
                $enabled = true;
            }
            if($item->count() == 1 && !empty($item->first()['locations'])){
                $button_text = __('Not available');
            }
            return array(
                'button_text' => $button_text,
                'selected_price_id' => $selected_price_id,
                'enabled' => $enabled,
                $item,
            );
        });

        $price_buttons_text = array(
            'buy' => StripePriceType::button('one_time'),
            'subscribe' => StripePriceType::button('recurring'),
            'na' => __('Not available'),
            'select' => __('Select an option'),
        );

        return Inertia::render('Store/Packs/Index', [
            'page_title' => __('Memberships'),
            'header' => __('Memberships'),
            'packs' => $packs,
            'price_buttons' => $price_buttons,
            'price_buttons_text' => $price_buttons_text,
            'locations' => $locations,
        ]);
    }
}
