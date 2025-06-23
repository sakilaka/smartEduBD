<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('site_settings')->count();
        if ($count == 0) {
            DB::table('site_settings')->insert(
                [
                    [
                        'title'            => 'EDUCATION ERP',
                        'short_title'      => 'ERP',
                        'contact_email'    => 'support@test.com',
                        'feedback_email'   => 'support@test.com',
                        'mobile1'          => '0100000000000',
                        'mobile2'          => '0100000000000',
                        'address'          => 'Test Address',
                        'web'              => 'https://google.com',
                        'developed_by'     => 'OSHIT SUTRA DAR',
                        'developed_by_url' => 'https://google.com',
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ],
                ]
            );
        }
    }
}
