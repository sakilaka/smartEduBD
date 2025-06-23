<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('groups')->count();
        if ($count == 0) {
            DB::table('groups')->insert(
                [
                    [
                        'name' => 'General',
                        'sorting' => 1
                    ],
                    [
                        'name' => 'Science',
                        'sorting' => 2
                    ],
                    [
                        'name' => '	Business Studies',
                        'sorting' => 3
                    ],
                    [
                        'name' => 'Humanities',
                        'sorting' => 4
                    ]
                ]
            );
        }
    }
}
