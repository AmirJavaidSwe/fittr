<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class ConnectPartnerDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //TODO: check if partner database is ready and set up, run migrations if needed
        //setup flow of database should be ran as sync job after registration, probably after email confirmed if not signup with google

        //because this middleware runs on every request, $request->user() may not exist, simply allow request to pass deeper OR set connection otherwise
        $partner = $request->user();

        if(empty($partner) || !$partner->is_partner){
            return $next($request);
        }

        // abort_if(empty($partner) || !$partner->is_partner, 403, __('Unable to connect to database.'));

        //Set connection to database: (run time)
        Config::set('database.connections.mysql_partner', [
            'driver' => 'mysql',
            'host' => $partner->db_host,
            'port' => $partner->db_port,
            'database' => $partner->db_name,
            'username' => $partner->db_user,
            'password' => Crypt::decryptString($partner->db_password),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
        ]);

        return $next($request);
    }
}
