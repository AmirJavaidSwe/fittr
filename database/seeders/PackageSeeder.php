<?php

namespace Database\Seeders;

use DB;
use Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=PackageSeeder
     *
     * @return void
     */
    public function run()
    {
        if (!Storage::disk('seeders')->exists('data/packages.json')) {
            dump('data/packages.json file do not exist!');
            return;
        }
        $packages = json_decode(Storage::disk('seeders')->get('/data/packages.json'));
        foreach ($packages as $package) {
            DB::table('packages')->insert([
                'status' => $package->status,
                'is_private' => $package->is_private ?? 0,
                'title' => $package->title,
                'description' => $package->description,
                'tx_percent' => $package->tx_percent,
                'tx_fixed_fee' => $package->tx_fixed_fee,
                'fee_annually' => $package->fee_annually,
                'fee_monthly' => $package->fee_monthly,
                'monthly_fee_sites' => $package->monthly_fee_sites,
                'admin_users' => $package->admin_users,
                'max_sites' => $package->max_sites,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}