<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\StateType;
use App\Services\Shared\CacheMasterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSubdomain
{
    public function __construct(CacheMasterService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //get a list of existing subdomains (service stores)
        $subdomains = $this->cache->subdomains();
        $subdomain = $request->subdomain;

        //we don't need to let non-existing subdomains to enter the app. We can just abort the request or redirect them to custom page or somewhere else.
        abort_unless($subdomains->contains('val', $subdomain), 403, __('This service store does not exist.'));

        // Same middleware is used to setup proper database connection to be used on service store:
        if(!Config::has('database.connections.mysql_partner')){
            //get correct partner for service store subdomain
            $business_subdomain = $this->cache->business_settings()->where('key', 'subdomain')->firstWhere('val', $subdomain);
            $business_db_connection = $this->cache->businesses()->where('status', StateType::ACTIVE->value)->firstWhere('id', $business_subdomain->business_id);

            //abort if connection to partner database does not exist, otherwise set new connection
            if(empty($business_db_connection)){
                abort(403, __('The service store is not available.'));
            }
            Config::set('database.connections.mysql_partner', [
                'driver' => 'mysql',
                'host' => $business_db_connection->db_host,
                'port' => $business_db_connection->db_port,
                'database' => $business_db_connection->db_name,
                'username' => $business_db_connection->db_user,
                'password' => Crypt::decryptString($business_db_connection->db_password),
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => '',
                'strict'    => true,
            ]);

            Config::set('business_subdomain', $business_subdomain);
        }

        return $next($request);
    }
}
