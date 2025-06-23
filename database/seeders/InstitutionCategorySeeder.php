<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitutionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('institution_categories')->count();
        if ($count == 0) {
            DB::table('institution_categories')->insert(
                [
                    ['id' => 1, 'name' => 'Primary', 'created_at'  => now(), 'updated_at'  => now()],
                    ['id' => 2, 'name' => 'Secondary', 'created_at'  => now(), 'updated_at'  => now()],
                    ['id' => 3, 'name' => 'Higher Secondary', 'created_at'  => now(), 'updated_at'  => now()],
                ]
            );
        }
    }
}
