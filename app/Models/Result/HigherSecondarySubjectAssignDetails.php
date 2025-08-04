<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\Subject;

class HigherSecondarySubjectAssignDetails extends BaseModel
{

    protected $guarded = ['id'];
    public $timestamps = false;

    protected static $logName = "Subject Assign Details";

    public function subject()
    {
        return $this->belongsTo(Subject::class)->select('id', 'name_en');
    }
}
