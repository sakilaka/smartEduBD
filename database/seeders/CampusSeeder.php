<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('campuses')->count();
        if ($count == 0) {
            DB::table('campuses')->insert([
                [
                    'institution_id'    => 1,
                    'name'              => 'Test Campus',
                    'sorting'           => 1
                ]
            ]);
        }
    }
}
