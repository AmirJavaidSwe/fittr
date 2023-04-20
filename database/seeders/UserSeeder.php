<?php

namespace Database\Seeders;

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
        if (!Storage::disk('seeders')->exists('data/users.json')) {
            dump('data/users.json file do not exist!');
            return;
        }
        $users = json_decode(Storage::disk('seeders')->get('/data/users.json'));
        foreach ($users as $user) {
            DB::table('users')->insert([
                'role' => $user->role,
                'name' => $user->name, 
                'email' => $user->email,
                'password' => Hash::make($user->password), 
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
