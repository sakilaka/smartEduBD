<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $arr = [
            SiteSettingsSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            MenuSeeder::class,

            InstitutionCategorySeeder::class,
            InstitutionPackageSeeder::class,
            InstitutionSeeder::class,
            CampusSeeder::class,
            ShiftSeeder::class,
            MediumSeeder::class,
            GroupSeeder::class,
            AcademicSessionSeeder::class,
            AcademicClassSeeder::class,
            SectionSeeder::class,
            AccountHeadSeeder::class,
            PaymentGatewaySeeder::class,
            ExamSeeder::class,
            SubjectSeeder::class,
            PrimaryGradeSeeder::class,
            SecondaryGradeSeeder::class,
        ];

        $this->call($arr);
    }
}
