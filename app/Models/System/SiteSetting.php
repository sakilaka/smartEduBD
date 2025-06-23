<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Models\System;

use App\Models\Base\BaseModel;

class SiteSetting extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = 'Site Settings';

    public function getLogoAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function getLogoSmallAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function getFaviconAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
}
