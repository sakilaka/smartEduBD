<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\BaseModel;
use App\Models\Student;

class HigherSecondaryResultDetails extends BaseModel
{
    protected $fillable = [
        'higher_secondary_result_id',
        'student_id',
        'total_mark_without_additional',
        'total_mark',
        'gpa_without_additional',
        'gpa',
        'letter_grade',
        'result_status',
        'merit_position_in_department',
        'merit_position_in_class',
    ];
    public $timestamps = false;

    protected static $logName = "Result Details";

    public function marks()
    {
        return $this->hasMany(HigherSecondaryResultMarks::class, 'higher_secondary_result_details_id')
            ->select('id', 'higher_secondary_result_details_id', 'subject_id', 'ct_mark', 'cq_mark', 'mcq_mark', 'practical_mark', 'obtained_mark', 'total_mark', 'gpa', 'letter_grade', 'additional_subject', 'is_absent')
            ->oldest('sorting');
    }
    public function additional_marks()
    {
        return $this->hasOne(HigherSecondaryResultMarks::class, 'higher_secondary_result_details_id')
            ->select('id', 'higher_secondary_result_details_id', 'subject_id', 'ct_mark', 'cq_mark', 'mcq_mark', 'practical_mark', 'obtained_mark', 'total_mark', 'gpa', 'letter_grade', 'additional_subject', 'is_absent')
            ->with('subject')
            ->where('additional_subject', 1);
    }

    public function student()
    {
        return $this->belongsTo(Student::class)
            ->select('id', 'student_id', 'name', 'mobile', 'college_roll', 'fathers_name', 'mothers_name', 'reg_no', 'student_type');
    }
    public function result()
    {
        return $this->belongsTo(HigherSecondaryResult::class);
    }
}
