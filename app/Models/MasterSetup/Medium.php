<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\MasterSetup;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medium extends BaseModel
{
    use SoftDeletes;

    protected $table = 'mediums';
    protected $guarded = ['id'];

    protected static $logName = "Medium";
}
