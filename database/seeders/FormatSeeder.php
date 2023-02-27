<?php

namespace Database\Seeders;

use DB;
use Storage;
use Illuminate\Database\Seeder;

class FormatSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=FormatSeeder
     *
     * @return void
     */
    public function run()
    {
        if (!Storage::disk('seeders')->exists('data/formats.json')) {
            dump('data/formats.json file does not exist!');
            return;
        }
        $formats = json_decode(Storage::disk('seeders')->get('/data/formats.json'));
        if (!is_array($formats)) {
            dump('corrupt json file!');
            return;
        }
        foreach ($formats as $format) {
            DB::table('formats')->insert([
                'type' => $format->type,
                'format_string' => $format->format_string,
                'format_js' => $format->format_js ?? null,
                'example' => $format->example ?? null,
                'notes' => $format->notes ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
