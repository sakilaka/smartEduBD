<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\BaseModel;
use App\Models\Student;

class SecondaryResultDetails extends BaseModel
{
    protected $fillable = [
        'secondary_result_id',
        'student_id',
        'total_mark_without_additional',
        'total_mark',
        'gpa_without_additional',
        'gpa',
        'letter_grade',
        'result_status',
        'merit_position_in_shift',
        'merit_position_in_class',
        'merit_position_in_section',
    ];
    public $timestamps = false;

    protected static $logName = "Result Details";

    public function marks()
    {
        return $this->hasMany(SecondaryResultMarks::class, 'secondary_result_details_id')
            ->select(
                'id',
                'secondary_result_details_id',
                'combined_result_marks_id',
                'subject_id',
                'ct_mark',
                'cq_mark',
                'mcq_mark',
                'practical_mark',
                'obtained_mark',
                'total_mark',
                'gpa',
                'letter_grade',
                'fourth_subject',
                'combined_mark',
                'combined_gpa',
                'combined_letter_grade',
                'sorting',
            )
            ->oldest('sorting');
    }
    public function except_fourth_marks()
    {
        return $this->hasMany(SecondaryResultMarks::class, 'secondary_result_details_id')
            ->select(
                'id',
                'secondary_result_details_id',
                'combined_result_marks_id',
                'subject_id',
                'ct_mark',
                'cq_mark',
                'mcq_mark',
                'practical_mark',
                'obtained_mark',
                'total_mark',
                'gpa',
                'letter_grade',
                'fourth_subject',
                'combined_mark',
                'combined_gpa',
                'combined_letter_grade',
            )
            ->where('fourth_subject', 0)
            ->oldest('sorting');
    }
    public function fourth_marks()
    {
        return $this->hasOne(SecondaryResultMarks::class, 'secondary_result_details_id')
            ->select(
                'id',
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
                'fourth_subject'
            )
            ->where('fourth_subject', 1)
            ->oldest('sorting');
    }
    public function student()
    {
        return $this->belongsTo(Student::class)
            ->join('student_profiles as pro', 'students.id', '=', 'pro.student_id')
            ->select(
                'students.id',
                'students.name_en',
                'pro.fathers_name_en',
                'pro.mothers_name_en',
                'pro.roll_number',
                'pro.profile',
                'students.software_id',
            );
    }
    public function result()
    {
        return $this->belongsTo(SecondaryResult::class, 'secondary_result_id');
    }
}
