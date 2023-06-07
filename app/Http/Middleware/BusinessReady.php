<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BusinessReady
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(session()->has('business_seetings.default_currency')){
            return $next($request);
        }

        //we need to make sure business is setup and has currency, below is temporary setup
        $country_id = session('business_seetings.country_id');
        // *technically, partner can create prices with any currency stripe supports and dashboard loads default currency for the country used to register connected business
        // when we use API, price being created must have currency provided.
        // We can pull connected country from:
        // #1 call api \app\Services\Shared\StripeConnectService->retrieveAccount() and get country ISO, then lookup country by ISO and get currency
        // #2 get country_id from business_seetings session, get country_id and then lookup country and currency

        //#2:
        $country = \App\Models\Country::find($country_id);
        if(!$country){
            return redirect()->route('partner.settings.general-details')
                ->with('flash_type', 'error')
                ->with('flash_message', __('Please select default country and currency'))
                ->with('flash_timestamp', time());
        }

        session()->put('business_seetings.default_currency', strtolower($country->currency));

        return $next($request);
    }
}
