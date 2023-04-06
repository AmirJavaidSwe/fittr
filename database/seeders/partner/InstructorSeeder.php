<?php

namespace Database\Seeders\Partner;

use Database\Factories\Partner\InstructorFactory;
use Illuminate\Database\Seeder;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(InstructorFactory::class);
    }
}
