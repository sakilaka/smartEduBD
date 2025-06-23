<?php

/**
 * @Oshit Sutra Dhar
 */

namespace App\Models\System;

use App\Models\Base\ScopeModel;

class Permission extends ScopeModel {

    protected $guarded = ['id'];
    public $timestamps = false;

    public function parent() {
        return $this->belongsTo( Permission::class, 'parent_id' );
    }
    //EACH CATEGORY MIGHT HAVE MULTIPLE CHILDREN
    public function children() {
        return $this->hasMany( Permission::class, 'parent_id' );
    }
}
