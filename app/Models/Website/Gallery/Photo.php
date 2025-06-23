<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Models\Website\Gallery;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['thumb'];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    // Get & Set
    public function getImagesAttribute($value)
    {
        return !empty($value) ? json_decode($value, true) : '';
    }

    public function getThumbAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            $image = $this->images['resize0'] ?? null;
            return !empty($image) ? url('/') . "/storage/" . $image : null;
        }
    }
}
