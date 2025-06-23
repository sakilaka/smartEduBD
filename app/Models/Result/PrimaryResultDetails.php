<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\BaseModel;
use App\Models\Student;

class PrimaryResultDetails extends BaseModel
{
    protected $fillable = [
        'primary_result_id',
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
        return $this->hasMany(PrimaryResultMarks::class, 'primary_result_details_id')
            ->select('id', 'primary_result_details_id', 'subject_id', 'ct_mark', 'cq_mark', 'mcq_mark', 'obtained_mark', 'total_mark', 'gpa', 'letter_grade', 'fourth_subject')
            ->oldest('sorting');
    }
    public function except_fourth_marks()
    {
        return $this->hasMany(PrimaryResultMarks::class, 'primary_result_details_id')
            ->select('id', 'primary_result_details_id', 'subject_id', 'ct_mark', 'cq_mark', 'mcq_mark', 'obtained_mark', 'total_mark', 'gpa', 'letter_grade', 'fourth_subject')
            ->where('fourth_subject', 0)
            ->oldest('sorting');
    }
    public function fourth_marks()
    {
        return $this->hasOne(PrimaryResultMarks::class, 'primary_result_details_id')
            ->select('id', 'primary_result_details_id', 'subject_id', 'ct_mark', 'cq_mark', 'mcq_mark', 'obtained_mark', 'total_mark', 'gpa', 'letter_grade', 'fourth_subject')
            ->where('fourth_subject', 1)
            ->oldest('sorting');
    }
    public function student()
    {
        return $this->belongsTo(Student::class)
            ->join('student_profiles as profile', 'students.id', '=', 'profile.student_id')
            ->select(
                'students.id',
                'students.name_en',
                'profile.fathers_name_en',
                'profile.mothers_name_en',
                'profile.roll_number',
                'students.software_id',
                'students.software_id'
            );
    }
    public function result()
    {
        return $this->belongsTo(PrimaryResult::class, 'primary_result_id');
    }
}
