<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitutionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('packages')->count();
        if ($count == 0) {
            DB::table('packages')->insert(
                [
                    [
                        'name'      => 'Premium',
                        'duration'  => 12,
                        'amount'    => 12000,
                        'status'    => 'active',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]
                ]
            );
        }
    }
}
