<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\MasterSetup;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AcademicClassMapping;
use App\Models\Website\Notice;

class Institution extends BaseModel
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static $logName = "Institution";

    public function getLogoAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function getIdcardFrontPartAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function getIdcardBackPartAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function getAdmitCardImageAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function getPrimaryTermMarksheetAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function getSecondaryTermMarksheetAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function getSecondaryTermMarksheetCombinedAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }

    public function getAdminAdmitCardAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }

    public function getSeatCardAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }

    public function class_mappings()
    {
        return $this->hasMany(AcademicClassMapping::class, 'institution_id');
    }
    public function campuses()
    {
        return $this->hasMany(Campus::class, 'institution_id')
            ->select('id', 'institution_id', 'name');
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function notices()
    {
        return $this->hasMany(Notice::class, 'institution_id');
    }
    public function categories()
    {
        return $this->hasMany(InstitutionCategory::class, 'institution_id');
    }

    /**
     * Logged user institution id
     */
    public static function current()
    {
        $authID = auth()->id();
        return session()->get("institution_{$authID}")['id'] ?? null;
    }
}
