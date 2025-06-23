<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends BaseModel
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static $logName = "Designation";
}
