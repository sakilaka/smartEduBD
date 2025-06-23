<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('exams')->count();
        if ($count == 0) {
            DB::table('exams')->insert([
                [
                    'institution_category_id' => 1,
                    'name' => 'Class Test One',
                    'exam_type' => 'ct',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'institution_category_id' => 1,
                    'name' => '1st Term Examination',
                    'exam_type' => 'term',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'institution_category_id' => 1,
                    'name' => '2nd Term Examination',
                    'exam_type' => 'term',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'institution_category_id' => 1,
                    'name' => 'Annual Examination',
                    'exam_type' => 'term',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                [
                    'institution_category_id' => 2,
                    'name' => 'Class Test One',
                    'exam_type' => 'ct',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'institution_category_id' => 2,
                    'name' => '1st Term Examination',
                    'exam_type' => 'term',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'institution_category_id' => 2,
                    'name' => '2nd Term Examination',
                    'exam_type' => 'term',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'institution_category_id' => 2,
                    'name' => 'Annual Examination',
                    'exam_type' => 'term',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
