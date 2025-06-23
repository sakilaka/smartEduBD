<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\Institution;
use App\Models\MasterSetup\Medium;

class PrimarySubjectAssign extends BaseModel
{
    protected $guarded = ['id'];
    public $timestamps = false;

    protected static $logName = "Subject Assign";

    /**
     * Relations with others table
     *
     * @var array
     */
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
    public function details()
    {
        return $this->hasMany(PrimarySubjectAssignDetails::class, 'primary_subject_assign_id');
    }
}
