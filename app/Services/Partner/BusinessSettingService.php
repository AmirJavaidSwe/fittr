<?php

namespace App\Services\Partner;

use Storage;
use App\Enums\CastType;
use App\Enums\SettingKey;
use App\Enums\SettingGroup;
use App\Enums\StripePriceType;
use App\Events\BusinessSettingUpdated;
use App\Models\BusinessSetting;
use App\Models\Partner\Pack;
use App\Services\Shared\CacheMasterService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;

class BusinessSettingService
{
    public function __construct(BusinessSetting $model, CacheMasterService $cache)
    {
        $this->model = $model;
        $this->cache = $cache;
    }

    public function getByGroup(SettingGroup $group): array
    {
        $business = session('business');

        return $this->model->ofBusiness($business->id)->ofGroup($group)->get()->each(function($item) {
            $item->val = $this->getCastValue($item);
        })->pluck('val', 'key')->toArray();
    }

    public function getByGroups($groups): array
    {
        $business = session('business');

        return $this->model->ofBusiness($business->id)->ofGroups($groups)->get()->each(function($item) {
            $item->val = $this->getCastValue($item);
        })->pluck('val', 'key')->toArray();
    }

    public function getByKeys($keys = []): array
    {
        $business = session('business');
        $existing = $this->model->ofBusiness($business->id)->whereIn('key', $keys)->get()->each(function($item) {
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
        if(in_array($item->key, [SettingKey::time_format->value, SettingKey::date_format->value])){
            return $this->cache->formats()->firstWhere('id', $item->val);
        }
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
        $business = session('business');
        $business_id = $business->id;

        collect($request->validated())->each(function ($value, $key) use ($business_id) {
            if($value instanceof UploadedFile){
                $value = $value->storePublicly($key, ['disk' => config('filesystems.default')]);
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

            //clear subdomains in cache
            if(SettingKey::from($key)->name == 'subdomain' && (empty($previous_value) || $previous_value != $value)){
                $this->cache->put('cache_subdomains', null, 0);
            }

            if(in_array($key, SettingKey::files()) && $previous_value){
                Storage::disk(config('filesystems.default'))->delete($previous_value);
            }
        });

        //update session:
        $groups = array(
            SettingGroup::general_details,
            SettingGroup::general_address,
            SettingGroup::general_formats,
            SettingGroup::service_store_general,
            SettingGroup::service_store_header,
            SettingGroup::service_store_seo,
            SettingGroup::service_store_code,
            SettingGroup::bookings,
        );
        $settings = $this->getByGroups(array_column($groups, 'name'));
        $request->session()->put('business_seetings', $settings);

        //save to cache when settings were last updated timestamp (\App\Http\Middleware\AuthenticateSubdomain::class will update settings in session)
        $this->cache->put('business_seetings_updated.'.$business_id, now()->timestamp);

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
        $business = session('business');

        return BusinessSetting::where('business_id', '!=', $business->id)->where('key', $key)->where('val', $val)->doesntExist();
    }

    // Method to get Packs with prices having FAP enabled, used by Fair access policy setting
    public function getFapPacks()
    {
        return Pack::whereHas('prices')
            ->with(['prices' => function (Builder $query) {
                $query->where('type', StripePriceType::recurring->name)->where('is_unlimited', true)->where('is_fap', true);
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->reject(function ($item) {
                // keep packs having fap prices only
                return $item->prices->isEmpty();
            })
            ->values(); //reset keys, json
    }
}