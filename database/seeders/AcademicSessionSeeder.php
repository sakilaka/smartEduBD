<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('academic_sessions')->count();
        if ($count == 0) {
            DB::table('academic_sessions')->insert([
                [
                    'institution_category_id' => 1,
                    'name' => '2025-Primary',
                    'sorting' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'institution_category_id' => 2,
                    'name' => '2025-Secondary',
                    'sorting' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'institution_category_id' => 3,
                    'name' => '2024-2025',
                    'sorting' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
