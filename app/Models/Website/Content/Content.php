<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Models\Website\Content;

use App\Models\Base\BaseModel;

class Content extends BaseModel
{
    protected $guarded        = ['id'];
    protected static $logName = 'Content';

    protected $hidden = [
        'is_meta', 'meta',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name', 'logo');
    }
    public function contentFiles()
    {
        return $this->hasMany(ContentFile::class)->oldest('sorting');
    }
    public function getImageAttribute($value)
    {
        return (!empty($value)) ? url('/') . "/storage/$value" : null;
    }
}
