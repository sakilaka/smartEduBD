<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\MasterSetup\AcademicClass;
use Illuminate\Database\Eloquent\Model;

class DepartmentQualidaction extends Model {

    protected $fillable = ['department_id', 'academic_qualification_id', 'department_code'];
    public $timestamps  = false;

    // Relations
    public function qualification() {
        return $this->belongsTo( AcademicQualification::class, 'academic_qualification_id' )->active();
    }
    public function department() {
        return $this->belongsTo( Department::class, 'department_id' )->active();
    }
    public function academic_class() {
        return $this->hasMany( AcademicClass::class, 'academic_qualification_id', 'academic_qualification_id' )
            ->select( 'id', 'name', 'academic_qualification_id' );
    }
}