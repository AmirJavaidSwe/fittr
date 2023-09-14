<?php

namespace App\Services\Partner;

use App\Enums\PackType;
use App\Enums\StateType;
use App\Models\Partner\Pack;
use Illuminate\Contracts\Database\Eloquent\Builder;

class PackService
{
    public function canChangeType($new_type, $old_type): bool
    {
        if($new_type == $old_type){
            return true;
        }
        switch ($new_type) {
            case PackType::location_pass->name:
                return false;
            case PackType::corporate->name:
                return false;
            
            default:
                return in_array($new_type, [PackType::class_lesson->name, PackType::service->name, PackType::hybrid->name]);
        }
    }

    public function getGroupedPacks()
    {
        $labels = PackType::labels();
        $packs = Pack::with(['pack_prices.locations'])
        ->withCount([
            'memberships',
            'memberships as active_memberships_count' => function (Builder $query) {
                $query->where('status', StateType::get('active'));
            },
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('type')
        ->map(function ($collection, $key) use ($labels) {
            $label_key = array_search($key, array_column($labels, 'value'));
            return (object) array(
                'type' => $key,
                'pack_count' => $collection->count(),
                'packs' => $collection,
                'label' => $labels[$label_key],
            );
        })
        ->sortBy('label.order');

        return $packs;
    }

    public function normalizedPack($validated) : array
    {
        //INCOMPLETE
        $pack_type = $validated['type'];

        //clear private url, if pack is no longer private
        if($validated['is_private'] === false){
            $validated['private_url'] = null;
        }

        // clear existing restrictions when false
        if($validated['is_restricted'] === false){
            $validated['restrictions'] = null;
        }

        switch ($pack_type) {
            // case PackType::creditable($pack->type):
            case PackType::get('class_lesson'):
                //turn off is_restricted if no restrictions set
                if($validated['is_restricted'] === true){
                    $offpeak = $validated['restrictions']['offpeak'];
                    $classtypes = $validated['restrictions']['classtypes'];
                    $validated['is_restricted'] = $offpeak !== false || !empty($classtypes);
                }
                break;

            case PackType::get('service'):
                //turn off is_restricted if no restrictions set
                if($validated['is_restricted'] === true){
                    $offpeak = $validated['restrictions']['offpeak'];
                    $servicetypes = $validated['restrictions']['servicetypes'];
                    $validated['is_restricted'] = $offpeak !== false || !empty($servicetypes);
                }
                break;

            case PackType::get('hybrid'):
                if($validated['is_restricted'] === true){
                    $offpeak = $validated['restrictions']['offpeak'];
                    $classtypes = $validated['restrictions']['classtypes'];
                    $servicetypes = $validated['restrictions']['servicetypes'];
                    $validated['is_restricted'] = $offpeak !== false || !empty($classtypes) || !empty($servicetypes);
                }

            case PackType::get('location_pass'):
                if($validated['is_restricted'] === true){
                    $validated['is_restricted'] = false;
                }
                $validated['restrictions'] = null;
                break;

            case PackType::get('corporate'):
                if($validated['is_restricted'] === true){
                    $validated['is_restricted'] = $validated['restrictions']['offpeak'] !== false;
                }
                break;
        }

        return $validated;
    }

    public function normalizedPrice(Pack $pack, $validated) : array
    {
        //INCOMPLETE
        switch ($pack->type) {
            case PackType::get('class_lesson'):
                // Set sessions = 0 if type == recurring && is_unlimited == true
                if($validated['type'] == 'recurring' && $validated['is_unlimited'] === true){
                    $validated['sessions'] = 0;
                }
                break;

            case PackType::get('service'):
                // Set sessions = 0 if type == recurring && is_unlimited == true
                if($validated['type'] == 'recurring' && $validated['is_unlimited'] === true){
                    $validated['sessions'] = 0;
                }
                break;

            case PackType::get('hybrid'):
                // Set sessions = 0 if type == recurring && is_unlimited == true
                if($validated['type'] == 'recurring' && $validated['is_unlimited'] === true){
                    $validated['sessions'] = 0;
                }
                break;

            case PackType::get('location_pass'):
                // Set sessions = 0 if pass_mode === false
                if($validated['pass_mode'] === false){
                    $validated['sessions'] = 0;
                    $validated['is_renewable'] = false;
                }
                // clear expiration periods
                if($validated['is_expiring'] === false){
                    $validated['expiration'] = null;
                    $validated['expiration_period'] = null;
                }
                break;
            case PackType::get('corporate'):
                break;
        }

        unset($validated['pass_mode']);

        return $validated;
    }
}