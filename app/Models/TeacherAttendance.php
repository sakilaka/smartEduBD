<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;

class TeacherAttendance extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Teacher Attendance";

    public function details()
    {
        return $this->hasMany(TeacherAttendanceDetails::class);
    }
}
