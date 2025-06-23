<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('admins')->count();
        if ($count == 0) {
            DB::table('admins')->insert(
                [
                    [
                        'role_id'           => 1,
                        'name'              => 'Super Admin',
                        'mobile'            => '01800000000',
                        'email'             => 'superadmin@gmail.com',
                        'password'          => Hash::make('superadmin@123'),
                        'status'            => 'active',
                        'remember_token'    => Str::random(10),
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ],
                    [
                        'role_id'           => 2,
                        'name'              => 'Institution Admin',
                        'mobile'            => '01800000000',
                        'email'             => 'institution@gmail.com',
                        'password'          => Hash::make('institution@123'),
                        'status'            => 'active',
                        'remember_token'    => Str::random(10),
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ],
                ]
            );
        }
    }
}
