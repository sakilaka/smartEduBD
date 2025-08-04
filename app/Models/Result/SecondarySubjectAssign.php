<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\Group;
use App\Models\MasterSetup\Institution;
use App\Models\MasterSetup\Medium;
use App\Models\MasterSetup\Subject;
use App\Models\Student;

class SecondarySubjectAssign extends BaseModel
{
    protected $guarded = ['id'];
    public $timestamps = false;

    protected static $logName = "Subject Assign";

  
    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name', 'short_name');
    }
    public function medium()
    {
        return $this->belongsTo(Medium::class)->select('id', 'name');
    }
    public function academic_class()
    {
        return $this->belongsTo(AcademicClass::class)->select('id', 'name');
    }
    public function academic_group()
    {
        return $this->belongsTo(Group::class)->select('id', 'name');
    }
    public function details()
    {
        return $this->hasMany(SecondarySubjectAssignDetails::class, 'secondary_subject_assign_id')
            ->oldest('sorting');
    }

    public function student()
    {
        return $this->belongsTo(Student::class)->select('id', 'group_id', 'academic_class_id', 'name');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class)->select('id',  'name_en');
    }
}
