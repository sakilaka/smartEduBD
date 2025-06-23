<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('payment_gateways')->count();
        if ($count == 0) {
            DB::table('payment_gateways')->insert(
                [
                    [
                        'institution_id'    => 1,
                        'academic_class_id' => 1,
                        'title'          => "TEST",
                        'provider'       => "SSL",
                        'options'        => '[
                                {
                                    "method": "SSL",
                                    "prefix": "SSL",
                                    "gateway": "SSL",
                                    "password": "totbd5f250d33954e4@ssl",
                                    "username": "totbd5f250d33954e4",
                                    "account_no": "62394D2FD0B2539913"
                                }
                            ]',
                        'status'         => 'active',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ],
                ]
            );
        }
    }
}
