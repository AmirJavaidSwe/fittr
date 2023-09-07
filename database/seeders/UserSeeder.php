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
                'is_super' => $user->is_super ?? true, 
                'source' => $user->source,
                'name' => $user->name, 
                'email' => $user->email,
                'password' => Hash::make($user->password), 
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            if(!empty($user->users)){
                foreach ($user->users as $partner_admin) {
                    $partnerId = DB::table('users')->insertGetId([
                        'business_id' => $partner_admin->business_id, 
                        'is_super' => $partner_admin->is_super ?? true, 
                        'source' => $partner_admin->source,
                        'name' => $partner_admin->name, 
                        'email' => $partner_admin->email,
                        'password' => Hash::make($partner_admin->password), 
                        'remember_token' => Str::random(10),
                        'email_verified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // $role = Role::where('source', $user->source)->whereSlug($user->source)->first();
            // DB::table('role_user')->insert([
            //     'role_id' => $role->id,
            //     'user_id' => $userId
            // ]);
        }
    }
}
