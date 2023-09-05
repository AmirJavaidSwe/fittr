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
            case PackType::default->name:
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
        $packs = Pack::with(['pack_prices'])
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
}