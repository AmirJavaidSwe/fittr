<?php

namespace App\Events;

use Illuminate\Support\Facades\Crypt;

class PartnerEvent
{
    // DO NOT use SerializesModels trait on child event class for partner events.
    // This trait will call __unserialize() method that will attempt to restore the model on mysql_partner connection when listener run
    // Every partner listener should call $this->setPartnerConnection($event) in order to set the DB connection (mysql_partner);

    /**
     * The Business DB connection.
     *
     * @var Array
     */
    public $business_db;

    /**
     * Business settings array.
     *
     * @var Array
     */
    public $business_settings;

    public function __construct()
    {
        $this->business_db = !empty(config('database.connections.mysql_partner')) ?
            ['password' => Crypt::encryptString(config('database.connections.mysql_partner.password'))] + config('database.connections.mysql_partner') :
            array(
                'business_id' => session('business.id'),
                'host' => session('business.db_host'),
                'port' => session('business.db_port'),
                'database' => session('business.db_name'),
                'username' => session('business.db_user'),
                'password' => session('business.db_password'),
            );

        $this->business_settings = session('business_settings');
        if(empty($this->business_settings)){
            $business_setting_service = app('App\Services\Partner\BusinessSettingService');
            $this->business_settings = $business_setting_service->getByBusinessId($this->business_db['business_id']);
        }
    }
}
