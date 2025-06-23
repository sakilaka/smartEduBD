<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\ParentModel;

class AcademicQualification extends ParentModel
{
    protected $guarded = ['id'];

    protected static $logName = "Academic Qualification";

    // Relations
    public function department_qualifications()
    {
        $department_id = false;
        $user = auth()->guard('admin')->user() ?? null;

        if (is_object($user)) {
            if ($user->type == 'Admin' && !empty($user->department_id)) {
                $department_id = [$user->department_id];
            } else if ($user->type == 'Teacher') {
                $department_id = TeacherSubjectAssign::getAssignId('department_id');
            }
        }

        return $this->hasMany(DepartmentQualidaction::class, 'academic_qualification_id')
            ->when($department_id, function ($q) use ($department_id) {
                $q->whereIn('department_id', $department_id);
            })
            ->whereHas('department', function ($q) {
                $q->where('status', 'active');
            })
            ->with('department:id,name,registration,application_fee');
    }

    // get / set
    public function getAdmissionFilesAttribute($val)
    {
        return !empty($val) ? json_decode($val, true) : [];
    }

    // Department Conditions
    public function scopeDept($query)
    {
        $department_id = auth()->guard('admin')->user()->department_id ?? null;
        if (!empty($department_id)) {
            $query->where('department_id', $department_id);
        }
    }
}
