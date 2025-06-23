<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;

class TeacherAttendanceDetails extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Teacher Attendance Details";

    public function teacher()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
