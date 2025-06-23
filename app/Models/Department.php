<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\ParentModel;

class Department extends ParentModel {

    protected $guarded = ['id'];
    public $appends    = ['dept_qualifications'];

    protected static $logName = "Department";

    // Department Conditions
    public function scopeDept( $query ) {
        $department_id = auth()->guard( 'admin' )->user()->department_id ?? null;
        if ( !empty( $department_id ) ) {
            $query->where( 'id', $department_id );
        }
    }

    // Relations
    public function invoice() {
        return $this->hasMany( Invoice::class, 'department_id' );
    }
    public function department_qualifications() {
        return $this->hasMany( DepartmentQualidaction::class, 'department_id' );
    }

    // get & set
    public function getDeptQualificationsAttribute() {
        return [];
    }
}