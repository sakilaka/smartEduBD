<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\ParentModel;
use App\Models\MasterSetup\Subject;

class PrimaryResultMarks extends ParentModel
{
    protected $fillable = [
        'primary_result_details_id',
        'subject_id',
        'ct_mark',
        'cq_mark',
        'mcq_mark',
        'obtained_mark',
        'total_mark',
        'gpa',
        'letter_grade',
        'pass_marks',
        'fourth_subject',
        'highest_marks',
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
        return $this->belongsTo(Subject::class)->select('id', 'name_en');
    }
}
