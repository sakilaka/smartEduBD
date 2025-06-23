<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('mediums')->count();
        if ($count == 0) {
            DB::table('mediums')->insert(
                [
                    [
                        'name' => 'Bangla Medium',
                        'sorting' => 1,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ],
                    [
                        'name' => 'English Medium',
                        'sorting' => 2,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]
                ]
            );
        }
    }
}
