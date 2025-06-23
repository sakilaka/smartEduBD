<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\ParentModel;
use App\Models\MasterSetup\Subject;

class SecondaryClassTestResultMarks extends ParentModel
{
    protected $fillable = [
        'secondary_class_test_result_details_id',
        'subject_id',
        'mark',
        'exam_mark',
        'pass_mark',
        'result_status'
    ];

    protected static $logName = "Class Test Result Marks";

    public function subject()
    {
        return $this->belongsTo(Subject::class)->select('id', 'name_en');
    }

    public function class_test_result_details()
    {
        return $this->belongsTo(SecondaryClassTestResultDetails::class);
    }
}
