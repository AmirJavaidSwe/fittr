<?php

namespace Database\Seeders\Partner;

use App\Models\Partner\Pack;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class PackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $packs = Pack::factory()
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
