<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Attendance;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class AttendanceDetails extends Model
{

    protected $fillable = ['attendance_id', 'software_id', 'in_time', 'out_time', 'type', 'device_identifier', 'rfid', 'status'];

    protected static $logName = "Attendance Details";

    public function student()
    {
        return $this->hasOne(Student::class, 'software_id', 'software_id')
            ->with(
                'guardian:id,name_en,mobile',
                'profile:student_id,roll_number,profile'
            )->select('id', 'guardian_id', 'software_id', 'name_en', 'status');
    }
}
