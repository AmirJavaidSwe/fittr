<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            $userId = DB::table('users')->insertGetId([
                'name' => $user->name, 
                'role' => $user->role,
                'email' => $user->email,
                'password' => Hash::make($user->password), 
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $role = Role::whereSlug($user->role)->first();
            DB::table('role_user')->insert([
                'role_id' => $role->id,
                'user_id' => $userId
            ]);
        }
    }
}
