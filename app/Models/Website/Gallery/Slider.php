<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Models\Website\Gallery;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $guarded = [];

    public function getSliderAttribute($value)
    {
        return (!empty($value)) ? url('/') . "/storage/$value" : null;
    }
}
