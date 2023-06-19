<?php

namespace App\Http\Controllers\Partner;

use DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\BusinessOnboardRequest;
use App\Enums\FormatType;
use App\Enums\StateType;
use App\Models\Business;
use App\Models\BusinessSetting;
use App\Models\Country;
use App\Models\Format;
use App\Models\Timezone;
use App\Services\Partner\BusinessSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PartnerOnboardController extends Controller
{
    public function __construct(BusinessSettingService $business_setting_service)
    {
        $this->business_setting_service = $business_setting_service;
    }

    public function index(Request $request)
    {
        $partner = $request->user();
        if(!empty($partner->business_id)){
            // return $this->redirectBackSuccess(__('Onboarding process complete! Edit your settings on this page.'), 'partner.settings.index');
            return redirect()->route('partner.settings.index');
        }

        return Inertia::render('Onboarding', [
            'page_title' => __('Onboarding'),
            'header' => array(
                [
                    'title' => __('Onboarding'),
                    'link' => '',
                ]
            ),
            'countries' => Country::where('status', true)->get(),
            'timezones' => Timezone::where('status', true)->orderBy('title', 'asc')->get(),
            'date_format' => Format::where('type', FormatType::date->name)->first()->id,
            'time_format' => Format::where('type', FormatType::time->name)->first()->id,
            'partner' => $request->user(),
        ]);
    }

    public function update(BusinessOnboardRequest $request)
    {
        //subdomain extra uniqueness check
        $is_unique = BusinessSetting::where('key', 'subdomain')->where('val', $request->subdomain)->doesntExist();
        Validator::make(['unique' => $is_unique], ['unique' => 'accepted'], ['unique.accepted' => __('The subdomain has already been taken.')])->validate();

        $partner = $request->user();

        $db_name = 'fittr_p'.$partner->id;
        $db_host = config('database.connections.mysql.host');
        $db_port = config('database.connections.mysql.port');
        $db_master_partner_username = config('database.connections.mysql.master_partner_username');
        $db_user = 'p_'.$partner->id;
        $db_password = Str::random(8).'Aa7!';
        
        //create new business
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

        //save newly created business into session
        $request->session()->put('business', $business);

        //save business setting from request
        $this->business_setting_service->update($request);

        //create new partner database
        try {
            Schema::dropDatabaseIfExists($db_name);
            Schema::createDatabase($db_name);

            //drop database user
            DB::statement("DROP USER IF EXISTS '$db_user'@'$db_host'");
            //create new DB user
            DB::statement("CREATE USER '$db_user'@'$db_host' IDENTIFIED BY '$db_password'");

            //give new user priveledges to work on created database
            DB::statement("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'$db_host'");
            DB::statement("GRANT ALL PRIVILEGES ON $db_name.* TO '$db_master_partner_username'@'%'");

            //create runtime config
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
            DB::setDefaultConnection('mysql_partner');

            //run migrations on currently connected partner database:
            $exitCode = Artisan::call('migrate', ['--path' => 'database/migrations/partner', '--force' => true]);

            DB::purge('mysql_partner'); //Disconnect from the given database and remove from local cache.

        } catch (\Exception $e) {
            //report DB error
        }
        return redirect()->route($partner->dashboard_route);
    }
}
