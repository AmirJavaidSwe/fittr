<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\SettingGroup;
use App\Services\Partner\BusinessSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class ConnectPartnerDatabase
{
    public function __construct(BusinessSettingService $business_settings_service)
    {
        $this->business_settings_service = $business_settings_service;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //TODO: check if partner database is ready and set up, run migrations if needed
        //setup flow of database should be ran as sync job after registration, probably after email confirmed if not signup with google

        //because this middleware runs on every request, $request->user() may not exist, simply allow request to pass deeper OR set connection otherwise
        $partner = $request->user();

        if(empty($partner) || !$partner->is_partner){
            return $next($request);
        }

        //grab business from session. Session may not have $business, use $partner->business as default
        $business = session('business', $partner->business);

        abort_if(empty($business), 403, __('Unable to connect.'));

        //This condition will happen after login
        if ($request->session()->missing('business')) {
            $request->session()->put('business', $business);
        }
        if ($request->session()->missing('business_seetings')) {
            $groups = array(
                SettingGroup::general_details,
                SettingGroup::general_address,
                SettingGroup::general_formats,
                SettingGroup::service_store_general,
                SettingGroup::service_store_header,
                SettingGroup::service_store_seo,
                SettingGroup::service_store_code,
            );
            $settings = $this->business_settings_service->getByGroups(array_column($groups, 'name'));
            $request->session()->put('business_seetings', $settings);
        }

        //Set connection to database: (run time)
        Config::set('database.connections.mysql_partner', [
            'driver' => 'mysql',
            'host' => $business->db_host,
            'port' => $business->db_port,
            'database' => $business->db_name,
            'username' => $business->db_user,
            'password' => Crypt::decryptString($business->db_password),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
        ]);

        return $next($request);
    }
}
