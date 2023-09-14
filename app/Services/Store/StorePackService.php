<?php

namespace App\Services\Store;

use App\Enums\StripePriceType;
use App\Models\Partner\Pack;
use Illuminate\Contracts\Database\Eloquent\Builder;

class StorePackService
{
    public function activePacks(): \Illuminate\Database\Eloquent\Collection
    {
        return Pack::active()
            ->public()
            //filter out packs without price options
            ->whereHas('pack_prices', function (Builder $query) {
                $query->where('is_active', true);
            })
            ->with([
                'pack_prices' => function (Builder $query) {
                    //include active prices only
                    $query->where('is_active', true);
                },
                'pack_prices.locations'
            ])
        ->get()
        ->map(function($pack) {
            //copy related collection
            $pack_prices = $pack->pack_prices;
            //remove original related collection
            unset($pack->pack_prices);
            //inject new collection for related
            $pack->pack_prices = $pack_prices->filter(function($pack_price){
                if($pack_price->locations->isEmpty()){
                    //keep location unrestricted prices
                    return true;
                }
                //remove the price unless at least one location is active, pack_price locations will have full set, including inactive locations
                return $pack_price->locations->where('status', true)->isNotEmpty();
            })
            //reset for json
            ->values();

            return $pack;
        });
    }

    public function buttonsText(): array
    {
        return array(
            'buy' => StripePriceType::button('one_time'),
            'subscribe' => StripePriceType::button('recurring'),
            'na' => __('Not available'),
            'select' => __('Select an option'),
        );
    }

    public function priceButtons($prices): \Illuminate\Support\Collection
    {
        $price_buttons = $prices->map(function ($item) {
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

        return $price_buttons;
    }
}