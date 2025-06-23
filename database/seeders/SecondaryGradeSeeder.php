<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecondaryGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('secondary_grade_management')->count();
        if ($count == 0) {
            DB::table('secondary_grade_management')->insert([
                [
                    "id" => 7,
                    "from_mark" => "79.50",
                    "to_mark" => "100.00",
                    "grade" => "A+",
                    "gpa" => "5.00",
                    "from_gpa" => "5.00",
                    "to_gpa" => "5.00",
                ],
                [
                    "id" => 6,
                    "from_mark" => "69.50",
                    "to_mark" => "79.49",
                    "grade" => "A",
                    "gpa" => "4.00",
                    "from_gpa" => "4.00",
                    "to_gpa" => "4.99",
                ],
                [
                    "id" => 5,
                    "from_mark" => "59.50",
                    "to_mark" => "69.49",
                    "grade" => "A-",
                    "gpa" => "3.50",
                    "from_gpa" => "3.50",
                    "to_gpa" => "3.99",
                ],
                [
                    "id" => 4,
                    "from_mark" => "49.50",
                    "to_mark" => "59.49",
                    "grade" => "B",
                    "gpa" => "3.00",
                    "from_gpa" => "3.00",
                    "to_gpa" => "3.49",
                ],
                [
                    "id" => 3,
                    "from_mark" => "39.50",
                    "to_mark" => "49.49",
                    "grade" => "C",
                    "gpa" => "2.00",
                    "from_gpa" => "2.00",
                    "to_gpa" => "2.99",
                ],
                [
                    "id" => 2,
                    "from_mark" => "32.50",
                    "to_mark" => "39.49",
                    "grade" => "D",
                    "gpa" => "1.00",
                    "from_gpa" => "1.00",
                    "to_gpa" => "1.99",
                ],
                [
                    "id" => 1,
                    "from_mark" => "0.00",
                    "to_mark" => "32.49",
                    "grade" => "F",
                    "gpa" => "0.00",
                    "from_gpa" => "0.00",
                    "to_gpa" => "0.00",
                ],
            ]);
        }
    }
}
