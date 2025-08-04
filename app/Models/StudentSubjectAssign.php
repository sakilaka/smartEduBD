<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\Subject;

class StudentSubjectAssign extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "StudentSubjectAssign";

    public function student()
    {
        return $this->belongsTo(Student::class)->select('id', 'department_id', 'academic_class_id', 'name');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class)->select('id',  'name_en');
    }
}
