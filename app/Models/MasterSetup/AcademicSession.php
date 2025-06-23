<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\MasterSetup;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSession extends BaseModel
{
    protected $guarded = ['id'];

    use SoftDeletes;

    protected static $logName = "Academic Session";

    public function institution_category()
    {
        return $this->belongsTo(InstitutionCategory::class)->select('id', 'name');
    }
}
