<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class PartnerListener
{
    /**
     * Business settings array.
     *
     * @var Array
     */
    public $business_seetings;

    /**
     * Set mysql_partner connection.
     *
     * @return void
     */
    public function setPartnerConnection($event): void
    {
        $this->business_seetings = $event->business_seetings;

        Config::set('database.connections.mysql_partner', [
            'driver' => 'mysql',
            'host' => $event->business_db['host'],
            'port' => $event->business_db['port'],
            'database' => $event->business_db['database'],
            'username' => $event->business_db['username'],
            'password' => Crypt::decryptString($event->business_db['password']),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
        ]);
    }
}
