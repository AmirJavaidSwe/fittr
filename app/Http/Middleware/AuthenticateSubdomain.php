<?php

namespace App\Http\Middleware;

use Closure;
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

        if(!Config::has('database.connections.mysql_partner')){
            $partner_settings = $this->cache->partner_settings()->where('key', 'subdomain')->firstWhere('val', $subdomain);
            $partner_db_connection = $this->cache->partner_db_connections()->firstWhere('id', $partner_settings->partner_id);

            //abort if connection to partner database does not exist, otherwise set new connection
            if(empty($partner_db_connection)){
                abort(403, __('The service store connection is lost.'));
            }
            Config::set('database.connections.mysql_partner', [
                'driver' => 'mysql',
                'host' => $partner_db_connection->db_host,
                'port' => $partner_db_connection->db_port,
                'database' => $partner_db_connection->db_name,
                'username' => $partner_db_connection->db_user,
                'password' => Crypt::decryptString($partner_db_connection->db_password),
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => '',
                'strict'    => true,
            ]);
        }

        return $next($request);
    }
}
