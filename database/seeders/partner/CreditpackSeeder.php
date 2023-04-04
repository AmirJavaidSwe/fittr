<?php

namespace Database\Seeders\Partner;

use App\Models\Creditpack;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class CreditpackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return Creditpack::factory()
            ->count(3)
            ->state(
                new Sequence(
                    ['status' => true],
                    ['status' => false],
                )
            )
            ->sequence(fn (Sequence $sequence) => ['title' => 'Demo pack '.$sequence->index+1])
            ->create();
    }
}
