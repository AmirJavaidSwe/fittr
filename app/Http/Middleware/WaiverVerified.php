<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Models\Partner\UserWaiver;
use Symfony\Component\HttpFoundation\Response;

class WaiverVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()) {
            return $next($request);
        }
        if($request->session()->has('all-signup-waiver-signed')) {
            $allSignupWaiverSigned = $request->session()->get('all-signup-waiver-signed');
            if($allSignupWaiverSigned === true) {
                return $next($request);
            }
        }
        $waivers = Waiver::where('show_at', 'sign-up')->where('is_active', 1)->get();
        if (!$waivers->isEmpty()) {
            $waiver_ids = $waivers->pluck('id')->toArray();
            $user_waiver_count = UserWaiver::where('user_id', auth()->user()->id)
            ->whereIn('waiver_id', $waiver_ids)->count();

            if (count($waiver_ids) == $user_waiver_count) {
                $request->session()->put('all-signup-waiver-signed', true);
                return $next($request);
            } else {
                return redirect(route('ss.waiver-verification', ["subdomain" => request()->session()->get('business_settings')['subdomain']]));
            }
        }
        return $next($request);
    }

}
