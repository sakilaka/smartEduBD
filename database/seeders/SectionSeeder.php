<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('sections')->count();
        if ($count == 0) {
            DB::table('sections')->insert(
                [
                    [
                        'name' => 'Section A',
                        'sorting' => 1,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ],
                    [
                        'name' => 'Section B',
                        'sorting' => 2,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ],
                    [
                        'name' => 'Section C',
                        'sorting' => 3,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ],
                    [
                        'name' => 'Section D',
                        'sorting' => 4,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]
                ]
            );
        }
    }
}
