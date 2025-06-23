<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('roles')->count();
        if ($count == 0) {
            DB::table('roles')->insert(
                [
                    [
                        'name'         => 'Super Admin',
                        'status'       => 'active',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ],
                    [
                        'name'         => 'Institution Admin',
                        'status'       => 'active',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]
                ]
            );
        }
    }
}
