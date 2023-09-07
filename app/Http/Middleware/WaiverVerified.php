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
        $waiver = Waiver::where('show_at', 'sign-up')->where('is_active', 1)->first();
        if(!$waiver) {
            return $next($request);
        }
        if($waiver) {
            $user_waiver = UserWaiver::where('user_id', auth()->user()->id)
            ->where('waiver_id', $waiver->id)->first();
            if($user_waiver) {
                return $next($request);
            } else {
                if((!($waiver->sign_again)) && Carbon::parse($waiver->created_at) > Carbon::parse(auth()->user()->created_at)) {
                    return $next($request);
                }
                return redirect(route('ss.waiver-verification', ["subdomain" => request()->session()->get('business_settings')['subdomain']]));
            }
        }
        return $next($request);
    }
}
