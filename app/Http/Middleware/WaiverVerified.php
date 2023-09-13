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
        if(!auth()->user()) {
            return $next($request);
        }
        $waivers = Waiver::where('show_at', 'sign-up')->where('is_active', 1)->get();
        if(!$waivers) {
            return $next($request);
        }
        if(count($waivers) > 0) {
            $countWaivers = 0;
            foreach($waivers as $waiver){
                $user_waiver = UserWaiver::where('user_id', auth()->user()->id)
                ->where('waiver_id', $waiver->id)->exists();
                if($user_waiver){
                    $countWaivers++;
                }
            }
            if($countWaivers == count($waivers)){
                return $next($request);
            }else {
            foreach($waivers as $waiver){
                if((!($waiver->sign_again)) && Carbon::parse($waiver->created_at) > Carbon::parse(auth()->user()->created_at)) {
                    return $next($request);
                }
            }
            return redirect(route('ss.waiver-verification', ["subdomain" => request()->session()->get('business_settings')['subdomain']]));
        }
        return $next($request);
    }
}
}
