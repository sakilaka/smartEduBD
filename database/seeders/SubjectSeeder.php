<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('subjects')->count();
        if ($count == 0) {
            DB::table('subjects')->insert([
                [
                    'id' => 1,
                    'institution_category_id' => 1,
                    'name_en' => 'Bangla',
                    'name_bn' => 'বাংলা',
                    'sorting' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'institution_category_id' => 1,
                    'name_en' => 'English',
                    'name_bn' => 'ইংরেজি',
                    'sorting' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3,
                    'institution_category_id' => 1,
                    'name_en' => 'Mathematics',
                    'name_bn' => 'গণিত',
                    'sorting' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 4,
                    'institution_category_id' => 1,
                    'name_en' => 'Science',
                    'name_bn' => 'বিজ্ঞান',
                    'sorting' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 5,
                    'institution_category_id' => 1,
                    'name_en' => 'Religion and moral education',
                    'name_bn' => 'ধর্ম ও নৈতিক শিক্ষা',
                    'sorting' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 6,
                    'institution_category_id' => 1,
                    'name_en' => 'Bangladesh and Global Studies',
                    'name_bn' => 'বাংলাদেশ ও বিশ্ব পরিচয়',
                    'sorting' => 6,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 7,
                    'institution_category_id' => 1,
                    'name_en' => 'Arts & Craft',
                    'name_bn' => 'অংকন',
                    'sorting' => 7,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 8,
                    'institution_category_id' => 2,
                    'name_en' => 'Bangla',
                    'name_bn' => 'বাংলা',
                    'sorting' => 8,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 9,
                    'institution_category_id' => 2,
                    'name_en' => 'English',
                    'name_bn' => 'ইংরেজি',
                    'sorting' => 9,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 10,
                    'institution_category_id' => 2,
                    'name_en' => 'Mathematics',
                    'name_bn' => 'গণিত',
                    'sorting' => 10,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 11,
                    'institution_category_id' => 2,
                    'name_en' => 'Science',
                    'name_bn' => 'বিজ্ঞান',
                    'sorting' => 11,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 12,
                    'institution_category_id' => 2,
                    'name_en' => 'Religious Education',
                    'name_bn' => 'ধর্ম শিক্ষা',
                    'sorting' => 12,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 13,
                    'institution_category_id' => 2,
                    'name_en' => 'Digital Technology',
                    'name_bn' => 'ডিজিটাল প্রযুক্তি',
                    'sorting' => 13,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 14,
                    'institution_category_id' => 2,
                    'name_en' => 'History and Social Science',
                    'name_bn' => 'ইতিহাস ও সামাজিক বিজ্ঞান',
                    'sorting' => 14,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 15,
                    'institution_category_id' => 2,
                    'name_en' => 'Wellbeing',
                    'name_bn' => 'স্বাস্থ্য সুরক্ষা',
                    'sorting' => 15,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 16,
                    'institution_category_id' => 2,
                    'name_en' => 'Art and Culture',
                    'name_bn' => 'শিল্প ও সংস্কৃতি',
                    'sorting' => 16,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 17,
                    'institution_category_id' => 2,
                    'name_en' => 'Life and Livelihood',
                    'name_bn' => 'জীবন ও জীবিকা',
                    'sorting' => 17,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
