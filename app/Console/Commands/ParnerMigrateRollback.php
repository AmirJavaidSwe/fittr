<?php

namespace App\Console\Commands;

use DB;
use App\Models\Business;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Artisan;

class ParnerMigrateRollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:parner-migrate:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback migrations on partner databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $businesses = Business::get();

        DB::purge('mysql'); //Disconnect from master database

        foreach ($businesses as $business) {
            $db_name = $business->db_name;
            $db_host = $business->db_host;
            $db_port = $business->db_port;
            $db_user = $business->db_user;
            $db_password = Crypt::decryptString($business->db_password);

            //create runtime config (mysql_partner - does not exist in the file)
            Config::set('database.connections.mysql_partner', [
                'driver' => 'mysql',
                'host' => $db_host,
                'port' => $db_port,
                'database' => $db_name,
                'username' => $db_user,
                'password' => $db_password,
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => '',
                'strict'    => true,
            ]);

            DB::purge('mysql_partner'); //Disconnect from the given database and remove from local cache.
            DB::setDefaultConnection('mysql_partner');

            //run migrations on currently connected partner database:
            Artisan::call('migrate:rollback', ['--path' => 'database/migrations/partner', '--force' => true]);

            dump('running rollback migrations on partner db: '.$db_name);
        }

        DB::reconnect('mysql'); //Reconnect to master database.
    }
}
