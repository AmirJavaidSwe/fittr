<?php

namespace App\Services\Shared;

use DB;
use Cache;

class CacheMasterService
{
    public function __construct()
    {

    }

    public function business_settings()
    {
        return Cache::remember('cache_business_settings', config('cache.ttl.default'), function () {
            return DB::table('business_settings')->get();
        });
    }

    public function businesses()
    {
        return Cache::remember('cache_businesses', config('cache.ttl.default'), function () {
            return DB::table('businesses')->select(
                'id',
                'status',
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
            return DB::table('business_settings')->select('val')->where('key', 'subdomain')->get();
        });
    }

    public function formats()
    {
        return Cache::remember('cache_formats', config('cache.ttl.default'), function () {
            return DB::table('formats')->select('id', 'type', 'format_string', 'format_js')->get();
        });
    }

}