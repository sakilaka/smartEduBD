<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\ParentModel;
use App\Models\MasterSetup\Subject;

class SecondaryResultMarks extends ParentModel
{
    protected $fillable = [
        'secondary_result_details_id',
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
        'fourth_subject',
        'highest_marks',
        'combined_result_marks_id',
        'combined_mark',
        'combined_gpa',
        'combined_letter_grade',
        'sorting',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['pass_marks' => 'array'];

    protected static $logName = "Result Marks";

    public function combined_subject()
    {
        return $this->hasOne(SecondaryResultMarks::class, 'combined_result_marks_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class)->select('id', 'name_en');
    }
}
