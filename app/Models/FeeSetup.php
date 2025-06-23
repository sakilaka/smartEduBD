<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\ParentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\Medium;
use App\Models\MasterSetup\Group;
use App\Models\MasterSetup\Institution;
use App\Models\MasterSetup\Shift;

class FeeSetup extends ParentModel
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static $logName = "Fee Setup";

    /**
     * Relations
     *
     * @var array
     */
    public function details()
    {
        return $this->hasMany(FeeSetupDetails::class);
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
}
