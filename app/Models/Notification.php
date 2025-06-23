<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {

    protected $guarded = ['id'];
    protected $appends = ['duration'];

    public function getDurationAttribute() {
        return Carbon::parse( $this->created_at )->diffForHumans();
    }
}
