<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrimaryGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('primary_grade_management')->count();
        if ($count == 0) {
            DB::table('primary_grade_management')->insert([
                [
                    "id" => 7,
                    "from_mark" => "80.00",
                    "to_mark" => "100.00",
                    "grade" => "A+",
                    "gpa" => "5.00",
                    "from_gpa" => "5.00",
                    "to_gpa" => "5.00",
                ],
                [
                    "id" => 6,
                    "from_mark" => "70.00",
                    "to_mark" => "79.99",
                    "grade" => "A",
                    "gpa" => "4.00",
                    "from_gpa" => "4.00",
                    "to_gpa" => "4.99",
                ],
                [
                    "id" => 5,
                    "from_mark" => "60.00",
                    "to_mark" => "69.99",
                    "grade" => "A-",
                    "gpa" => "3.50",
                    "from_gpa" => "3.50",
                    "to_gpa" => "3.99",
                ],
                [
                    "id" => 4,
                    "from_mark" => "50.00",
                    "to_mark" => "59.99",
                    "grade" => "B",
                    "gpa" => "3.00",
                    "from_gpa" => "3.00",
                    "to_gpa" => "3.49",
                ],
                [
                    "id" => 3,
                    "from_mark" => "40.00",
                    "to_mark" => "49.99",
                    "grade" => "C",
                    "gpa" => "2.00",
                    "from_gpa" => "2.00",
                    "to_gpa" => "2.99",
                ],
                [
                    "id" => 2,
                    "from_mark" => "33.00",
                    "to_mark" => "39.99",
                    "grade" => "D",
                    "gpa" => "1.00",
                    "from_gpa" => "1.00",
                    "to_gpa" => "1.99",
                ],
                [
                    "id" => 1,
                    "from_mark" => "0.00",
                    "to_mark" => "32.99",
                    "grade" => "F",
                    "gpa" => "0.00",
                    "from_gpa" => "0.00",
                    "to_gpa" => "0.00",
                ],
            ]);
        }
    }
}
