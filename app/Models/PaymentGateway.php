<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\Institution;
use App\Models\MasterSetup\AcademicClass;

class PaymentGateway extends BaseModel
{
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];

    protected static $logName = "PaymentGateway";

    /**
     * Relations with others table
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name');
    }
    public function academic_class()
    {
        return $this->belongsTo(AcademicClass::class)->select('id', 'name');
    }

    public function getOptionsAttribute($value)
    {
        return empty($value) ? [['gateway' => null]] : json_decode($value, true);
    }
}
