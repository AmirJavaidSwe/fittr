<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\User;
use DB;
use Hash;
use Str;
use Storage;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=UserSeeder
     *
     * @return void
     */
    public function run()
    {
        $business_id = config('database.connections.mysql_partner.business_id');
        dump('seeding members (fittr admins - are regular members here)');

        if (!Storage::disk('seeders')->exists('/partner/data/users.json')) {
            dump('/partner/data/users.json file does not exist!');
            return;
        }
        $users = json_decode(Storage::disk('seeders')->get('/partner/data/users.json'));
        foreach ($users as $user) {
            DB::table('users')->insert([
                'role' => $user->role,
                'stripe_id' => $user->stripe_id ?? null,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => Hash::make($user->password),
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        dump('seeding instructors');
        $instructors = json_decode(Storage::disk('seeders')->get('/partner/data/instructors.json'));

        foreach ($instructors as $instructor) {
            DB::table('users')->insert([
                'first_name' => $instructor->first_name,
                'last_name' => $instructor->last_name,
                'email' => $instructor->email,
                'role' => $instructor->role,
                'password' => Hash::make($instructor->password),
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // User::factory()->count(5)->create();
    }
}
