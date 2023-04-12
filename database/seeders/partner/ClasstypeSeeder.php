<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\Classtype;
use Illuminate\Database\Seeder;

class ClasstypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Classtype::factory()->count(5)->create();
    }
}
