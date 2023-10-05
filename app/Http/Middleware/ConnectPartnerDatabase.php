<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\SettingGroup;
use App\Services\Partner\BusinessSettingService;
use App\Services\Partner\DatabaseConnectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ConnectPartnerDatabase
{
    public function __construct(BusinessSettingService $business_settings_service, DatabaseConnectionService $db_service)
    {
        $this->business_settings_service = $business_settings_service;
        $this->db_service = $db_service;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response | \Inertia\Response
    {
        $partner = $request->user();
        //this is brand new user and business does not exist yet nor database is created
        if(empty($partner->business_id)){
            return to_route('partner.onboarding.index');
        }

        //grab business from session. Session may not have $business, use $partner->business as default
        $business = session('business', $partner->business);

        abort_if(empty($business), 403, __('Unable to connect.'));

        //This condition will happen after login
        if ($request->session()->missing('business')) {
            $request->session()->put('business', $business);
        }
        if ($request->session()->missing('business_settings')) {
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
            $request->session()->put('business_settings', $settings);
        }

         // set subdomain to runtime config, used by 'auth.subdomain' middleware
         Config::set('subdomain', [
            'name' => session('business_settings.subdomain'),
            'business_id' => $business->id,
        ]);

        //Set connection to database: (run time)
        $this->db_service->connect($business);

        return $next($request);
    }
}
