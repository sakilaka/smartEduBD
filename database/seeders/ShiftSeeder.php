<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('shifts')->count();
        if ($count == 0) {
            DB::table('shifts')->insert(
                [
                    [
                        'name' => 'Morning',
                        'sorting' => 1,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ],
                    [
                        'name' => 'Day',
                        'sorting' => 2,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]
                ]
            );
        }
    }
}
