<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;

class StudentProfile extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Student Profile";

    public function getProfileAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }

    public function setDobAttribute($date)
    {
        $this->attributes['dob'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
}
