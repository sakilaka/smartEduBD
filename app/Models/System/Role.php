<?php

namespace App\Models\System;

use App\Models\Base\ScopeModel;
use App\Models\User;

class Role extends ScopeModel {

    protected $guarded = ['id'];

    public function users() {
        return $this->hasMany( User::class );
    }

    public function permissions() {
        return $this->belongsToMany( Permission::class, 'role_permissions', 'role_id', 'permission_id' );
    }
}
