<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\Subject;

class TeacherSubjectAssign extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Teacher Subject Assign";

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public static function getAssignId($pluck_id, $req = [])
    {
        $level = $req['academic_qualification_id'] ?? false;
        $dept = $req['department_id'] ?? false;
        $class = $req['academic_class_id'] ?? false;

        $result = TeacherSubjectAssign::where('admin_id', auth()->id() ?? null)
            ->when($level, function ($q)  use ($level) {
                $q->where('academic_qualification_id', $level);
            })
            ->when($dept, function ($q)  use ($dept) {
                $q->where('department_id', $dept);
            })
            ->when($class, function ($q)  use ($class) {
                $q->where('academic_class_id', $class);
            })
            ->groupBy($pluck_id)
            ->pluck($pluck_id);

        return !empty($result) ? $result->toArray() : [];
    }
}
