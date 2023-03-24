<?php

namespace Database\Seeders;

use DB;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstanceSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=InstanceSeeder
     *
     * @return void
     */
    public function run()
    {
        $demo_partner = User::partner()->where('email', 'dummy1@fittr.tech')->first();
        if(!$demo_partner){
            return;
        }
        DB::table('instances')->insert([
            'partner_id' => $demo_partner->id,
            'name' => 'Ubuntu-partner-app',
            'status' => 'running',
            'public_ip' => '13.40.170.157',
            'private_ip' => '172.26.4.102',
            'subdomain' => 'demo.fittr.tech',
            'created_at' => now()
        ]);
    }
}