<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\ParentModel;

class StudentPromotion extends ParentModel {
    protected $guarded = ['id'];

    protected static $logName = "Student Promotion";

    // Json Decode
    public function getOldJsonAttribute( $val ) {
        return json_decode( $val, true );
    }
    public function getNewJsonAttribute( $val ) {
        return json_decode( $val, true );
    }
}
