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

    public function partner_db_connections()
    {
        return Cache::remember('cache_partner_partner_db_connections', config('cache.ttl.default'), function () {
            return DB::table('users')->select(
                'id',
                'db_host',
                'db_port',
                'db_name',
                'db_user',
                'db_password',
            )
            ->where('role', AppUserRole::PARTNER->value)
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