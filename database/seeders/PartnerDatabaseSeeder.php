<?php

namespace Database\Seeders;

use App\Enums\CastType;
use App\Enums\SettingKey;
use App\Enums\SettingGroup;
use App\Enums\StateType;
use App\Models\Business;
use App\Models\Format;
use App\Models\User;
use DB;
use Storage;
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
        //clear cache, it has subdomains and business
        Artisan::call('cache:clear');

        $partners = User::partner()/*->databaseless()*/->where('email', 'LIKE', 'dummy%')->get();

        $db_master_partner_username = config('database.connections.mysql.master_partner_username');
        $db_master_partner_password = config('database.connections.mysql.master_partner_password');
        DB::statement("DROP USER IF EXISTS '$db_master_partner_username'@'%'"); //backup user is case encrypted password corrupted
        DB::statement("CREATE USER '$db_master_partner_username'@'%' IDENTIFIED BY '$db_master_partner_password'");

        //Create new database for each Partner user (dummy only)
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
                    'status' => StateType::get('active'),
                    'db_host' => $db_host,
                    'db_port' => $db_port,
                    'db_user' => $db_user,
                    'db_password' => Crypt::encryptString($db_password),
                ]
            );

            dump('creating business for partner_id: '.$partner->id);

            // default date and time settings for business
            $business->settings()->updateOrCreate(
                ['key' => SettingKey::get('date_format')],
                [
                    'group_name' => SettingGroup::get('general_formats'),
                    'cast_to' => CastType::get('integer'),
                    'val' => Format::where('type', 'date')->where('format_string', 'Y-m-d')->first()->id,
                ]
            );
            dump('setting up business settings: date_format');

            $business->settings()->updateOrCreate(
                ['key' => SettingKey::get('time_format')],
                [
                    'group_name' => SettingGroup::get('general_formats'),
                    'cast_to' => CastType::get('integer'),
                    'val' => Format::where('type', 'time')->where('format_string', 'H:i')->first()->id,
                ]
            );
            dump('setting up business settings: time_format');

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

            dump('creating partner db with name: '.$db_name);
            dump('--');
        }
        dump('---FINISHED PARTNER DB CREATIONS---');

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
                'business_id' => $business->id,
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

            dump('running migrations on partner db: '.$db_name);

            $has_seeder_data = false;
            if (Storage::disk('seeders')->exists('/partner/data/business_'.$business->id.'/all.json')) {
                dump($business->id. ' business seeder data used');
                $data = json_decode(Storage::disk('seeders')->get('/partner/data/business_'.$business->id.'/all.json'));
                $has_seeder_data = true;
            }
            if($has_seeder_data == false){
                continue;
            }
            $business->update(['stripe_account_id' => $data->stripe_account_id]);
            foreach ($data->business_settings as $business_setting) {
                $business->settings()->updateOrCreate(
                    ['key' => $business_setting->key],
                    [
                        'group_name' => $business_setting->group_name,
                        'cast_to' => $business_setting->cast_to,
                        'val' => $business_setting->val,
                    ]
                );
            }

            // Artisan::call('db:seed --force Database\\\Seeders\\\Partner\\\DatabaseSeeder');
            Artisan::call('db:seed', ['--class' => 'Database\Seeders\Partner\DatabaseSeeder', '--force' => true]);
        }

        DB::reconnect('mysql'); //Reconnect to master database.
    }
}
