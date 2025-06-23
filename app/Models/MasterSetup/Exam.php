<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\MasterSetup;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends BaseModel
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static $logName = "Exam";

    public function class_test_exam()
    {
        return $this->hasOne(Exam::class, 'id', 'class_test_exam_id')->select('class_test_exam_id', 'name', 'id');
    }
    public function institution_category()
    {
        return $this->belongsTo(InstitutionCategory::class)->select('id', 'name');
    }
}
