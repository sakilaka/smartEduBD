<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\MasterSetup;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends BaseModel
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static $logName = "Subject";

    public function institution_category()
    {
        return $this->belongsTo(InstitutionCategory::class)->select('id', 'name');
    }
}
