<?php

namespace App\Services\Partner;

use Storage;
use App\Enums\CastType;
use App\Enums\SettingKey;
use App\Enums\SettingGroup;
use App\Events\BusinessSettingUpdated;
use App\Models\BusinessSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;

class BusinessSettingService
{
    public function __construct(BusinessSetting $model)
    {
        $this->model = $model;
    }

    public function getByGroup(SettingGroup $group): array
    {
        return $this->model->ofGroup($group)->get()->each(function($item) {
            $item->val = $this->getCastValue($item);
        })->pluck('val', 'key')->toArray();
    }

    public function getByKeys($keys = []): array
    {
        $existing = $this->model->whereIn('key', $keys)->get()->each(function($item) {
            $item->val = $this->getCastValue($item);
        })->pluck('val', 'key')->toArray();

        //inject original keys to resulted array
        $results = array();
        foreach ($keys as $key) {
            $results[$key] = $existing[$key] ?? null;
        }

        return $results;
    }

    public function getCastValue(BusinessSetting $item)
    {
        $value = $item->is_encrypted ? Crypt::decryptString($item->val) : $item->val;
        return match ($item->cast_to) {
                CastType::string->name => $value,
                CastType::integer->name => intval($value),
                CastType::float->name => floatval($value),
                CastType::boolean->name => boolval($value),
                CastType::array->name => json_decode($value),
                CastType::json->name => json_decode($value),
            };
    }

    public function update($request): void
    {
        $partner = $request->user();
        $business = $partner->business;
        $business_id = $business->id;

        collect($request->validated())->each(function ($value, $key) use ($business_id) {
            if($value instanceof UploadedFile){
                $value = $value->storePublicly($key, ['disk' => 'public']);
            }
            $identifier = ['business_id' => $business_id, 'key' => $key];
            $is_encrypted = SettingKey::from($key)->encryption();
            $values = [
                'group_name' => SettingKey::from($key)->group(),
                'cast_to' => SettingKey::from($key)->cast(),
                'is_encrypted' => $is_encrypted,
                'val' => ($is_encrypted ? Crypt::encryptString($value) : $value),
            ];
            $business_setting = BusinessSetting::where($identifier)->first();
            $previous_value = $business_setting->val ?? null;
            $business_setting ? $business_setting->update($values) : BusinessSetting::create($identifier + $values);

            if(in_array($key, SettingKey::files()) && $previous_value){
                Storage::disk('public')->delete($previous_value);
            }
        });
        BusinessSettingUpdated::dispatch($business_id);
    }

    public function getDefaultData($key): ?string
    {
        $defaults = json_decode(Storage::disk('seeders')->get('/defaults/settings_keys.json'));

        return $defaults->{$key} ?? null;
    }

    // Method to check uniqueness of value inside business_settings table
    public function uniqueSettingValue($key, $val): bool
    {
        return BusinessSetting::where('business_id', '!=', auth()->user()->business_id)->where('key', $key)->where('val', $val)->doesntExist();
    }
}