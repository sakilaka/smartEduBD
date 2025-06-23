<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Attendance;

use App\Models\Base\ParentModel;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\Campus;
use App\Models\MasterSetup\Group;
use App\Models\MasterSetup\Medium;
use App\Models\MasterSetup\Section;
use App\Models\MasterSetup\Shift;
use App\Models\Student;

class Attendance extends ParentModel
{
    protected $guarded = ['id'];
    protected $appends = ['total_student'];

    protected static $logName = "Attendance";

    /**
     * Relations with others table
     *
     * @var array
     */

    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name', 'short_name');
    }
    public function campus()
    {
        return $this->belongsTo(Campus::class)->select('id', 'name');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class)->select('id', 'name');
    }
    public function medium()
    {
        return $this->belongsTo(Medium::class)->select('id', 'name');
    }
    public function academic_class()
    {
        return $this->belongsTo(AcademicClass::class)->select('id', 'name');
    }
    public function group()
    {
        return $this->belongsTo(Group::class)->select('id', 'name');
    }
    public function section()
    {
        return $this->belongsTo(Section::class)->select('id', 'name');
    }
    public function details()
    {
        return $this->hasMany(AttendanceDetails::class);
    }
    public function present_student()
    {
        return $this->hasMany(AttendanceDetails::class)->where('status', 'P');
    }

    public function setDateAttribute($date)
    {
        $this->attributes['date'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }

    public function getTotalStudentAttribute()
    {
        return Student::where([
            'institution_id'    => $this->institution_id,
            'campus_id'         => $this->campus_id,
            'shift_id'          => $this->shift_id,
            'medium_id'         => $this->medium_id,
            'academic_class_id' => $this->academic_class_id,
            'group_id'          => $this->group_id,
            'section_id'        => $this->section_id,
            'status'            => 'active',
        ])->count();
    }
}
