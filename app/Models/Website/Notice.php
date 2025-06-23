<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Website;

use App\Models\Base\ParentModel;
use App\Models\MasterSetup\Institution;
use Illuminate\Support\Str;

class Notice extends ParentModel
{
    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($url) {
            $url->slug = Notice::createSlug($url->title);
        });
    }
    public static function createSlug($title)
    {
        $slug  = Str::slug($title);
        $count = Notice::whereLike('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . $count;
        }
        return $slug;
    }

    protected static $logName = "Notice";

    public function setDateAttribute($date)
    {
        $this->attributes['date'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }

    public function getImageAttribute($value)
    {
        return (!empty($value)) ? url('/') . "/storage/$value" : null;
    }
    public function assignables()
    {
        return $this->hasMany(NoticeAssignable::class, 'notice_id');
    }
    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name', 'logo');
    }
}
