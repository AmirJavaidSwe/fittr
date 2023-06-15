<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\SettingGroup;
use App\Enums\StateType;
use App\Services\Partner\BusinessSettingService;
use App\Services\Shared\CacheMasterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSubdomain
{
    public function __construct(BusinessSettingService $business_settings_service, CacheMasterService $cache)
    {
        $this->business_settings_service = $business_settings_service;
        $this->cache = $cache;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $subdomains = $this->cache->subdomains();
        $subdomain = $subdomains->firstWhere('val', $request->subdomain)->val ?? null;

        //we don't need to let non-existing subdomains to enter the app. We can just abort the request or redirect them to custom page or somewhere else.
        abort_if(empty($subdomain), 403, __('This service store does not exist.'));

        $business = $request->session()->has('business') ? 
                    session('business') :
                    $this->cache->businesses()->where('status', StateType::ACTIVE->value)->firstWhere('id', $subdomains->firstWhere('val', $subdomain)->business_id);

        // Same middleware is used to setup proper database connection to be used on service store:

        //abort if connection to partner database does not exist, otherwise set new connection
        if(empty($business)){
            abort(403, __('The service store is not available.'));
        }
        if ($request->session()->missing('business')) {
            $request->session()->put('business', $business);
        }

        // check if business settings have been recently updated
        $settings_updated = $this->cache->get('business_seetings_updated.'.$business->id);

        // store visitor may already have business_seetings stored in session, we need to update it when changes been made
        if($request->session()->has('business_seetings.settings_updated') && !empty($settings_updated)){
            $needs_refresh = session('business_seetings.settings_updated') < $settings_updated;
        }

        if ($request->session()->missing('business_seetings') || ($needs_refresh ?? false)) {
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
            $settings['settings_updated'] = now()->timestamp;
            $request->session()->put('business_seetings', $settings);
        }
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

        Config::set('app.name', session('business_seetings.business_name') ?? __('Service store'));

        return $next($request);
    }
}
