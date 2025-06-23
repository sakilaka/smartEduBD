<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\SMS;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsTemplate extends BaseModel
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static $logName = "SmsTemplate";
}
