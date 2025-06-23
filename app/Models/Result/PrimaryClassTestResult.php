<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\AcademicSession;
use App\Models\MasterSetup\Campus;
use App\Models\MasterSetup\Exam;
use App\Models\MasterSetup\Group;
use App\Models\MasterSetup\Institution;
use App\Models\MasterSetup\Medium;
use App\Models\MasterSetup\Section;
use App\Models\MasterSetup\Shift;

class PrimaryClassTestResult extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Class Test Result";

    public function details()
    {
        return $this->hasMany(PrimaryClassTestResultDetails::class);
    }
    public function academic_session()
    {
        return $this->belongsTo(AcademicSession::class)->select('id', 'name');
    }
    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name', 'short_name');
    }
    public function campus()
    {
        return $this->belongsTo(Campus::class)->select('id', 'name');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class)->select('id', 'name');
    }
    public function medium()
    {
        return $this->belongsTo(Medium::class)->select('id', 'name');
    }
    public function academic_class()
    {
        return $this->belongsTo(AcademicClass::class)->select('id', 'name');
    }
    public function group()
    {
        return $this->belongsTo(Group::class)->select('id', 'name');
    }
    public function section()
    {
        return $this->belongsTo(Section::class)->select('id', 'name');
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class)->select('id', 'name');
    }
    public function setPublishedDateAttribute($date)
    {
        $this->attributes['published_date'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
    public function setExamDateAttribute($date)
    {
        $this->attributes['exam_date'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
}
