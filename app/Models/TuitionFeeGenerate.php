<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\Institution;
use App\Models\MasterSetup\Medium;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\Group;
use App\Models\MasterSetup\Shift;

class TuitionFeeGenerate extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Tuition Fee Generate";

    /**
     * Relations with others table
     */
    public function details()
    {
        return $this->hasMany(TuitionFeeGenerateDetails::class);
    }
    public function head()
    {
        return $this->hasOne(AccountHead::class, 'id', 'account_head_id')->select('id', 'name_bn', 'name_en');
    }
    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name');
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
    public function shift()
    {
        return $this->belongsTo(Shift::class)->select('id', 'name');
    }

    /**
     * Get Hostel Fees
     *
     * @return \Illuminate\Http\Response
     */
    public static function hostelFeeGenerates()
    { }
}
