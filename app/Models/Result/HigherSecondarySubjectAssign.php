<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\AcademicQualification;
use App\Models\Base\BaseModel;
use App\Models\Department;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\Group;

class HigherSecondarySubjectAssign extends BaseModel {

    protected $guarded = ['id'];
    public $timestamps = false;

    protected static $logName = "Subject Assign";


    public function academic_class() {
        return $this->belongsTo( AcademicClass::class )->select( 'id', 'name' );
    }
    public function group() {
        return $this->belongsTo( Group::class )->select( 'id', 'name' );
    }
    public function details() {
        return $this->hasMany(HigherSecondarySubjectAssignDetails::class, 'subject_assign_id' );
    }
}
