<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\StateType;
use App\Services\Shared\CacheMasterService;

class InitSubdomain
{
    public function __construct( CacheMasterService $cache)
    {
        $this->cache = $cache;
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

        // set subdomain to runtime config, used by 'auth.subdomain' middleware
        Config::set('subdomain', [
            'name' => $subdomain->val,
            'business_id' => $subdomain->business_id,
        ]);

        return $next($request);
    }
}
