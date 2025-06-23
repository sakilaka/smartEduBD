<?php

/**
 * Dev: @OSHIT SUTRA DAR
 */

namespace App\Traits\Lib;

use App\Models\Department;
use App\Models\MasterSetup\Institution;
use App\Models\MasterSetup\Campus;
use App\Models\MasterSetup\Medium;
use App\Models\MasterSetup\Group;
use App\Models\MasterSetup\Section;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\AcademicSession;
use App\Models\MasterSetup\Shift;
use App\Models\MasterSetup\AccountHead;
use App\Models\MasterSetup\Exam;
use App\Models\MasterSetup\InstitutionCategory;
use App\Models\Result\PrimaryGradeManagement;

trait DynamicDataTrait
{
    protected function dynamicData()
    {
        $insID = Institution::current();

        $institutions = Institution::select('id', 'name')->with('class_mappings', 'campuses')
            ->when($insID, function ($q, $insID) {
                $q->where('id', $insID);
            })->get()->toArray();

        $campuses = Campus::select('id', 'institution_id', 'name')->when($insID, function ($q, $insID) {
            $q->where('institution_id', $insID);
        })->get()->toArray();

        $account_heads = AccountHead::active()->select('id', 'name_en', 'type')->get()->toArray();

        return compact(
            'institutions',
            'campuses',
            'account_heads',
        );
    }

    protected function commonDynamicData()
    {
        $mediums    = Medium::select('id', 'name')->get()->toArray();
        $departments      = Department::active()->select('name', 'id', 'registration', 'application_fee')->oldest('name')->get();
        $groups     = Group::select('id', 'name')->get()->toArray();
        $sections   = Section::select('id', 'name')->get()->toArray();
        $sessions   = AcademicSession::select('id', 'institution_category_id', 'name', 'current', 'online_admission')->get()->toArray();
        $classes    = AcademicClass::select('id', 'name', 'institution_category_id')->get()->toArray();
        $shifts     = Shift::select('id', 'name')->get()->toArray();
        $institution_categories = InstitutionCategory::select('id', 'name')->get();
        $exam = Exam::select('id', 'name', 'institution_category_id')->get()->toArray();

        return compact(
            'mediums',
            'departments',
            'groups',
            'sections',
            'sessions',
            'classes',
            'shifts',
            'institution_categories',
            'exam'
        );
    }
}
