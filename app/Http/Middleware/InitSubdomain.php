<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\StateType;
use App\Services\Shared\CacheMasterService;
use App\Services\Partner\DatabaseConnectionService;

class InitSubdomain
{
    public function __construct(DatabaseConnectionService $db_service, CacheMasterService $cache)
    {
        $this->cache = $cache;
        $this->db_service = $db_service;
    }

    /**
     * MUST run before session initiated.
     * all Fortify auth routes + all {subdomain} routes land here.
     * Subdomain routes and auth() subdomain routes must get verified, Fortify guard set, session connection set, DB mysql_partner connection set
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->host();
        $sub = strtok($host, '.');
        // 'app' subdomain may pass deeper into application and there's no need to change anything.
        if($sub === 'app'){
            return $next($request);
        }

        $subdomains = $this->cache->subdomains();
        $subdomain = $subdomains->firstWhere('val', $sub) ?? null;
        abort_if(empty($subdomain), 403, __('This service store does not exist.'));
        $business = $this->cache->businesses()->where('status', StateType::get('active'))->firstWhere('id', $subdomain->business_id);
        abort_if(empty($business), 403, __('This service store does not exist.'));

        // set Fortify login redirect, used in FortifyServiceProvider
        // Config::set('fortify.redirects.login', route('ss.home', ['subdomain' => $sub]));

        // set Fortify guard (see auth.php)
        Config::set('fortify.guard', 'store');

        // set Session driver connection. Note that we have to have mysql_partner connection ready for session to work.
        Config::set('session.connection', 'mysql_partner');

        // set session default auth driver. 
        // Below setting makes $request()->user() return logged in User model from partner database, instead of $request()->user('store') or auth()->user($guard = 'store')
        Auth::setDefaultDriver('store');

        //set the connection to partner database
        $this->db_service->connect($business);

        // set subdomain to runtime config, used by 'auth.subdomain' middleware
        Config::set('subdomain', [
            'name' => $subdomain->val,
            'business_id' => $subdomain->business_id,
        ]);

        return $next($request);
    }
}
