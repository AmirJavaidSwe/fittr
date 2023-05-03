<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\Classpack;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ClasspackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return Classpack::factory()
            ->count(3)
            ->state(
                new Sequence(
                    ['is_active' => true],
                    ['is_active' => false],
                )
            )
            ->sequence(fn (Sequence $sequence) => ['title' => 'Demo pack '.$sequence->index+1])
            ->create();
    }
}
