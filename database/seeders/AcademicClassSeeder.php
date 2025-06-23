<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('academic_classes')->count();
        if ($count == 0) {
            DB::table('academic_classes')->insert([
                [
                    'institution_category_id' => 1,
                    'name' => 'Baby',
                    'sorting' => 0
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'Pre Play',
                    'sorting' => 1
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'Play',
                    'sorting' => 2
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'Nursery',
                    'sorting' => 3
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'KG',
                    'sorting' => 4
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'One',
                    'sorting' => 5
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'Two',
                    'sorting' => 6
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'Three',
                    'sorting' => 7
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'Four',
                    'sorting' => 8
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'Five',
                    'sorting' => 9
                ],
                [
                    'institution_category_id' => 2,
                    'name' => 'Six',
                    'sorting' => 10
                ],
                [
                    'institution_category_id' => 2,
                    'name' => 'Seven',
                    'sorting' => 11
                ],
                [
                    'institution_category_id' => 2,
                    'name' => 'Eight',
                    'sorting' => 12
                ],
                [
                    'institution_category_id' => 2,
                    'name' => 'Nine',
                    'sorting' => 13
                ],
                [
                    'institution_category_id' => 2,
                    'name' => 'Ten',
                    'sorting' => 14
                ],
                [
                    'institution_category_id' => 3,
                    'name' => 'Eleventh',
                    'sorting' => 15
                ],
                [
                    'institution_category_id' => 3,
                    'name' => 'Twelve',
                    'sorting' => 16
                ]
            ]);
        }
    }
}
