<?php

namespace App\Services\Partner;

use Storage;
use App\Enums\CastType;
use App\Enums\SettingKey;
use App\Enums\SettingGroup;
use App\Events\PartnerSettingUpdated;
use App\Models\PartnerSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;

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

    public function getByKeys($keys = []): array
    {
        $existing = $this->model->loggedIn()->whereIn('key', $keys)->get()->each(function($item) {
            $item->val = $this->getCastValue($item);
        })->pluck('val', 'key')->toArray();

        //inject original keys to resulted array
        $results = array();
        foreach ($keys as $key) {
            $results[$key] = $existing[$key] ?? null;
        }

        return $results;
    }

    public function getCastValue(PartnerSetting $item)
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
        $partner_id = $request->user()->id;
        collect($request->validated())->each(function ($value, $key) use ($partner_id) {
            if($value instanceof UploadedFile){
                $value = $value->storePublicly($key, ['disk' => 'public']);
            }
            $identifier = ['partner_id' => $partner_id, 'key' => $key];
            $is_encrypted = SettingKey::from($key)->encryption();
            $values = [
                'group_name' => SettingKey::from($key)->group(),
                'cast_to' => SettingKey::from($key)->cast(),
                'is_encrypted' => $is_encrypted,
                'val' => ($is_encrypted ? Crypt::encryptString($value) : $value),
            ];
            $partner_setting = PartnerSetting::where($identifier)->first();
            $previous_value = $partner_setting->val ?? null;
            $partner_setting ? $partner_setting->update($values) : PartnerSetting::create($identifier + $values);

            if(in_array($key, SettingKey::files()) && $previous_value){
                Storage::disk('public')->delete($previous_value);
            }
        });
        PartnerSettingUpdated::dispatch($partner_id);
    }

    public function getDefaultData($key): ?string
    {
        $defaults = json_decode(Storage::disk('seeders')->get('/defaults/settings_keys.json'));

        return $defaults->{$key} ?? null;
    }

    // Method to check uniqueness of value inside partner_settings table (this will be updated to businesses later)
    public function uniqueSettingValue($key, $val): bool
    {
        return PartnerSetting::where('partner_id', '!=', auth()->user()->id)->where('key', $key)->where('val', $val)->doesntExist();
    }
}