<?php

namespace App\Services\Shared;

use DB;
use Cache;
use App\Enums\AppUserRole;

class CacheMasterService
{
    public function __construct()
    {

    }

    public function partner_settings()
    {
        return Cache::remember('cache_partner_settings', config('cache.ttl.default'), function () {
            return DB::table('partner_settings')->get();
        });
    }

    public function businesses()
    {
        return Cache::remember('cache_businesses', config('cache.ttl.default'), function () {
            return DB::table('businesses')->select(
                'id',
                'stripe_customer_id',
                'stripe_account_id',
                'db_host',
                'db_port',
                'db_name',
                'db_user',
                'db_password',
            )
            ->whereNull('deleted_at')
            ->get();
        });
    }

    public function subdomains()
    {
        return Cache::remember('cache_subdomains', config('cache.ttl.default'), function () {
            return DB::table('partner_settings')->select('val')->where('key', 'subdomain')->get();
        });
    }

}