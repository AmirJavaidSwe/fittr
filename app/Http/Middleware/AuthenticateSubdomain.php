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
     * This middleware runs on any entry to {subdomain} after 'init.subdomain' and after session init.
     * Jetstream routes (profile) may call this conditionally, depending on subdomain
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //get subdomain array having domain name and business_id from config, set inside 'init.subdomain' middleware
        $subdomain = config('subdomain');

        //we don't need to let non-existing subdomains to enter the app. We can just abort the request or redirect them to custom page or somewhere else.
        abort_if(empty($subdomain), 403, __('This service store does not exist.'));

        $business = $this->cache->businesses()->where('status', StateType::get('active'))->firstWhere('id', $subdomain['business_id']);
        if(empty($business)){
            abort(403, __('The service store is not available.'));
        }
        if ($request->session()->missing('business')) {
            $request->session()->put('business', $business);
        }

        // check if business settings have been recently updated
        $settings_updated = $this->cache->get('business_seetings_updated.'.$business->id);

        // store visitor may already have business_seetings stored in session, we need to update it when changes been made by admin
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
                SettingGroup::bookings,
            );
            $settings = $this->business_settings_service->getByGroups(array_column($groups, 'name'));
            $settings['settings_updated'] = now()->timestamp;
            $request->session()->put('business_seetings', $settings);
        }

        Config::set('app.name', session('business_seetings.business_name') ?? __('Service store'));

        return $next($request);
    }
}
