<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('institutions')->count();
        if ($count == 0) {
            DB::table('institutions')->insert([
                [
                    'package_id'    => 1,
                    'name'          => 'Test Institution',
                    'domain'        => 'https://www.google.com',
                    'address'       => 'Faridpur',
                    'status'        => 'active'
                ]
            ]);
        }
    }
}
