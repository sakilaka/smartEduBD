<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;

class Teacher extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Teacher";

    function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function setJoiningDateLecturerAttribute($date)
    {
        $this->attributes['joining_date_lecturer'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
    public function setJoiningDatePresentDesignationAttribute($date)
    {
        $this->attributes['joining_date_present_designation'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
    public function setJoiningDatePresentWorkStationAttribute($date)
    {
        $this->attributes['joining_date_present_work_station'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
    public function setDateOfBirthAttribute($date)
    {
        $this->attributes['date_of_birth'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }

    public function getSignatureAttribute($value)
    {
        return !empty($value) ? url('/') . "/public/storage/" . $value : null;
    }
}