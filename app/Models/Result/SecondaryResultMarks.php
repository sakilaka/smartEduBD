<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\ParentModel;
use App\Models\MasterSetup\Subject;
use App\Models\StudentSubjectAssign;

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

    public function subjectAssignment()
    {
        return $this->belongsTo(StudentSubjectAssign::class, 'subject_id', 'subject_id')
            ->where('student_id', function ($query) {
                $query->select('student_id')
                    ->from('secondary_result_details')
                    ->whereColumn('secondary_result_details.id', 'secondary_result_marks.secondary_result_details_id');
            })
            ->with('student.academic_class'); 
    }
}
