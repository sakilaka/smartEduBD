<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\ParentModel;
use App\Models\MasterSetup\Subject;

class HigherSecondaryResultMarks extends ParentModel
{
    protected $fillable = [
        'higher_secondary_result_details_id',
        'subject_id',
        'ct_mark',
        'cq_mark',
        'mcq_mark',
        'practical_mark',
        'obtained_mark',
        'total_mark',
        'gpa',
        'letter_grade',
        'pass_marks',
        'additional_subject',
        'is_absent',
        'sorting',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['pass_marks' => 'array'];

    protected static $logName = "Result Marks";

    public function subject()
    {
        return $this->belongsTo(Subject::class)->select('id', 'name_en', 'is_child');
    }
}
