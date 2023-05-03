<?php

namespace App\Services\Shared;

use DB;
use Cache;
use App\Models\Business;

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
            return Business::get();
        });
    }

    public function subdomains()
    {
        return Cache::remember('cache_subdomains', config('cache.ttl.default'), function () {
            return DB::table('business_settings')->select('business_id', 'val')->where('key', 'subdomain')->get();
        });
    }

    public function formats()
    {
        return Cache::remember('cache_formats', config('cache.ttl.default'), function () {
            return DB::table('formats')->select('id', 'type', 'format_string', 'format_js')->get();
        });
    }

    /* Setters, getters and wrappers */
    public function put(...$args) : void
    {
        Cache::put(...$args);
    }

    public function get($key, $default = null) 
    {
        return Cache::get($key, $default);
    }
}