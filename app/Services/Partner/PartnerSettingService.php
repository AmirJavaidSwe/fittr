<?php

namespace App\Services\Partner;

use App\Enums\CastType;
use App\Enums\SettingKey;
use App\Enums\SettingGroup;
use App\Events\PartnerSettingUpdated;
use App\Models\PartnerSetting;

class PartnerSettingService
{
    public function __construct(PartnerSetting $model)
    {
        $this->model = $model;
    }

    public function getByGroup(SettingGroup $group): array
    {
        return $this->model->loggedIn()->ofGroup($group)->get()->each(function($item) {
            $item->val = $this->getCastValue($item);
        })->pluck('val', 'key')->toArray();
    }

    public function getCastValue(PartnerSetting $item)
    {
        return match ($item->cast_to) {
                CastType::string->name => $item->val,
                CastType::integer->name => intval($item->val),
                CastType::float->name => floatval($item->val),
                CastType::boolean->name => boolval($item->val),
                CastType::array->name => json_decode($item->val),
                CastType::json->name => json_decode($item->val),
            };
    }

    public function update($request): void
    {
        $partner_id = $request->user()->id;
        collect($request->validated())->each(function ($value, $key) use ($partner_id) {
            PartnerSetting::updateOrCreate(
                ['partner_id' => $partner_id, 'key' => $key],
                [
                    'group_name' => SettingKey::from($key)->group(),
                    'cast_to' => SettingKey::from($key)->cast(),
                    'val' => $value
                ]
            );
        });
        PartnerSettingUpdated::dispatch($partner_id);
    }
}