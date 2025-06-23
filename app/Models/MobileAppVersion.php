<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;

class MobileAppVersion extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'android' => 'array',
        'ios' => 'array',
    ];

    public function getAndroidAttribute($value)
    {
        if (empty($value)) {
            return [
                "latest_version" => "",
                "minimum_version" => "",
                "force_update" => false,
                "maintenance_mode" => false,
            ];
        }
        return json_decode($value, true);
    }

    public function getIosAttribute($value)
    {
        if (empty($value)) {
            return [
                "latest_version" => "",
                "minimum_version" => "",
                "force_update" => false,
                "maintenance_mode" => false,
            ];
        }
        return json_decode($value, true);
    }

    protected static $logName = "Mobile App Version";
}
