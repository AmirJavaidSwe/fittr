<?php

namespace Database\Seeders;

use App\Enums\StateType;
use App\Models\Business;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Schema;

class PartnerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=PartnerDatabaseSeeder
     */
    public function run(): void
    {
        $partners = User::partner()/*->databaseless()*/->get();

        $db_master_partner_username = config('database.connections.mysql.master_partner_username');
        $db_master_partner_password = config('database.connections.mysql.master_partner_password');
        DB::statement("DROP USER IF EXISTS '$db_master_partner_username'@'%'"); //backup user is case encrypted password corrupted
        DB::statement("CREATE USER '$db_master_partner_username'@'%' IDENTIFIED BY '$db_master_partner_password'");

        //Create new database for each Partner user
        foreach ($partners as $partner) {
            $db_name = 'fittr_p'.$partner->id;
            $db_host = config('database.connections.mysql.host');
            $db_port = config('database.connections.mysql.port');
            $db_user = 'p_'.$partner->id;
            $db_password = Str::random(8).'Aa7!';

            // Create new Business (wrapper entity of partner and other staff users partner may create in future) and store connection to partner db
            $business = Business::updateOrCreate(
                [
                    'db_name' => $db_name,
                ],
                [
                    'status' => StateType::ACTIVE->value,
                    'db_host' => $db_host,
                    'db_port' => $db_port,
                    'db_user' => $db_user,
                    'db_password' => Crypt::encryptString($db_password),
                ]
            );

            // set business_id on partner
            $partner->business()->associate($business);
            $partner->save();

            //drop database user
            DB::statement("DROP USER IF EXISTS '$db_user'@'$db_host'");

            //drop partner database
            Schema::dropDatabaseIfExists($db_name);
            // DB::statement("DROP DATABASE IF EXISTS $db_name"); //Alternative

            //create new partner database
            Schema::createDatabase($db_name);
            // DB::statement("CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"); //Alternative

            //create new DB user
            DB::statement("CREATE USER '$db_user'@'$db_host' IDENTIFIED BY '$db_password'");

            //give new user priveledges to work on created database
            DB::statement("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'$db_host'");
            DB::statement("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_master_partner_username'@'%'");

            // 'php artisan key:generate' may be ran only after all encrypted values were retrived.
            // 'The MAC is invalid' will be thrown on attempt to decrypt values with new key.
            // $db_master_partner_username added to each db for recovery only, should not be used for regular queries
        }

        //run fresh migrations on each of partner database, we have to switch connection (so that migrations table be used inside partner db)
        DB::purge('mysql'); //Disconnect from master database
        
        // first partner only:
        $first_partner_id = $partners->first()->id;
        foreach ($partners as $partner) {
            $business = $partner->business;
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
            Artisan::call('migrate', ['--path' => 'database/migrations/partner', '--force' => true]);
            // Artisan::call('db:seed Partner\DatabaseSeeder --database=mysql_partner --class=UserSeeder');

            //run seeds for the first partner only
            if($partner->id == $first_partner_id){
                dump('seeding partner id '.$first_partner_id);
                dump($partner->email.' set stripe connected account for business '.$business->id);

                $business->update(['stripe_account_id' => 'acct_1MlaLVFZgNT1PRCW']);
                // Artisan::call('db:seed --force Database\\\Seeders\\\Partner\\\DatabaseSeeder');
                Artisan::call('db:seed', ['--class' => 'Database\Seeders\Partner\DatabaseSeeder', '--force' => true]);
            }
        }

        DB::reconnect('mysql'); //Reconnect to master database.
    }
}
