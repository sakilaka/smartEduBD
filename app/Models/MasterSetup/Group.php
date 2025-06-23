<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\MasterSetup;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends BaseModel
{
    use SoftDeletes;
    protected $guarded = ['id'];

    protected static $logName = "Group";
}
