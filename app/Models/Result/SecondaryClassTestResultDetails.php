<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Result;

use App\Models\Base\BaseModel;
use App\Models\Student;

class SecondaryClassTestResultDetails extends BaseModel
{
    protected $fillable = ['secondary_class_test_result_id', 'student_id', 'total_mark', 'result_status'];
    public $timestamps = false;

    protected static $logName = "Class Test Result Details";

    public function student()
    {
        return $this->belongsTo(Student::class)
            ->join('student_profiles as profile', 'students.id', '=', 'profile.student_id')
            ->select('students.id', 'students.name_en', 'profile.roll_number', 'students.software_id', 'students.software_id');
    }
    public function marks()
    {
        return $this->hasMany(SecondaryClassTestResultMarks::class, 'secondary_class_test_result_details_id')
            ->select('id', 'secondary_class_test_result_details_id', 'subject_id', 'mark', 'pass_mark', 'exam_mark', 'result_status');
    }
    public function class_test_result()
    {
        return $this->belongsTo(SecondaryClassTestResult::class, 'secondary_class_test_result_id');
    }
}
