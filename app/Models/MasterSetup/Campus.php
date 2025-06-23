<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\MasterSetup;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends BaseModel
{
    use SoftDeletes;
    protected $guarded = ['id'];

    protected static $logName = "Campus";

    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name');
    }
}
