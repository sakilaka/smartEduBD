<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('account_heads')->count();
        if ($count == 0) {
            DB::table('account_heads')->insert(
                [
                    [
                        'name_en'    => 'Admission Fees',
                        'name_bn'    => 'ভর্তি ফিস',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Session Charge & Others',
                        'name_bn'    => 'সেশন চার্জ ও অন্যান্য',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Admission Form Fee',
                        'name_bn'    => 'ভর্তি ফর্ম ফি',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Tuition Fees',
                        'name_bn'    => 'টিউশন ফিস',
                        'type'       => 'tuition',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => '1st Term Exam Fee',
                        'name_bn'    => '১ম সাময়িক পরীক্ষার ফিস',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => '2nd Term Exam Fee',
                        'name_bn'    => '২য় সাময়িক পরীক্ষার ফিস',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Yearly Exam Fees',
                        'name_bn'    => 'বার্ষিক পরীক্ষার ফিস',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Half Yearly Exam Fee',
                        'name_bn'    => 'অর্ধবার্ষিক পরীক্ষার ফিস',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Pre test Exam Fee',
                        'name_bn'    => 'প্রাক নির্বাচনী পরীক্ষার ফি',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Pre test Exam Fee',
                        'name_bn'    => 'প্রাক নির্বাচনী পরীক্ষার ফি',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Test Exam Fee',
                        'name_bn'    => 'নির্বাচনী পরীক্ষার ফি',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Others Fee',
                        'name_bn'    => 'অন্যান্য ফি',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Transfer Certificate',
                        'name_bn'    => 'ছাড়পত্র ফি',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'Registration Fee',
                        'name_bn'    => 'রেজিস্ট্রেশন ফি',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'SSC Form FillUp (Regular)',
                        'name_bn'    => 'এসএসসি ফর্ম পূরণ(নিয়মিত)',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'SSC Form Fillup (Irregular)',
                        'name_bn'    => 'এসএসসি ফর্ম পূরণ(অনিয়মিত)',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name_en'    => 'HSC Form Fillup (Regular)',
                        'name_bn'    => 'এইচএস সি ফর্ম পূরণ(নিয়মিত)',
                        'type'       => 'school',
                        'status'     => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]
            );
        }
    }
}
