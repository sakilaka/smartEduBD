<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\AccountHead;

class StudentDiscount extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Student Discount";

    public function account_head()
    {
        return $this->belongsTo(AccountHead::class)->select('id', 'name_en');
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
