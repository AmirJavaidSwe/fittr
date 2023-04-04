<?php

namespace Database\Seeders\Partner;

use App\Models\User;
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
        if (!Storage::disk('seeders')->exists('/partner/data/users.json')) {
            dump('/partner/data/users.json file does not exist!');
            return;
        }
        $users = json_decode(Storage::disk('seeders')->get('/partner/data/users.json'));
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

        $instructors = json_decode(Storage::disk('seeders')->get('/partner/data/instructors.json'));

        foreach ($instructors as $instructor) {
            DB::table('users')->insert([
                'name' => $instructor->name,
                'email' => $instructor->email,
                'role' => $instructor->role,
                'password' => Hash::make($instructor->password),
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        User::factory()->count(5)->create();
    }
}
