<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\ParentModel;
use App\Models\Department;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\AcademicSession;
use App\Models\MasterSetup\Exam;
use App\Models\MasterSetup\Group;

class HigherSecondaryResult extends ParentModel
{
    protected $guarded = ['id'];

    protected static $logName = "HigherSecondaryResult";

    public function details()
    {
        return $this->hasMany(HigherSecondaryResultDetails::class);
    }
    public function academic_session()
    {
        return $this->belongsTo(AcademicSession::class)->select('id', 'name');
    }
    public function academic_class()
    {
        return $this->belongsTo(AcademicClass::class)->select('id', 'name');
    }
    public function academic_group()
    {
        return $this->belongsTo(Group::class)->select('id', 'name');
    }
    // public function qualification()
    // {
    //     return $this->belongsTo(AcademicQualification::class, 'academic_qualification_id')->select('id', 'name');
    // }
    public function department()
    {
        return $this->belongsTo(Department::class)->select('id', 'name');
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class)->select('id', 'name');
    }

    public function setPublishedDateAttribute($date)
    {
        $this->attributes['published_date'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
}
