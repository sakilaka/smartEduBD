<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class ScopeModel extends Model {

    public function scopeDraft( $query ) {
        return $query->where( 'status', 'draft' );
    }

    public function scopeActive( $query ) {
        return $query->where( 'status', 'active' );
    }

    public function scopePending( $query ) {
        return $query->where( 'status', 'pending' );
    }
}
