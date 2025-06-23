<?php

namespace App\Http\Controllers\Api\Guardian\Traits;

trait FindResultTrait
{
    protected function getResult($model, $student_id, $request, $category = null)
    {
        $result_id = $request->result_id ?? false;

        $query = $model::with(
            'student',
            'result.academic_session',
            'result.institution',
            'result.campus',
            'result.shift',
            'result.medium',
            'result.academic_class',
            'result.group',
            'result.section',
            'result.exam'
        )->where('student_id', $student_id);

        if ($category == 2) {
            $query->with(['marks' => function ($q) {
                $q->with('subject', 'combined_subject.subject')->whereNull('combined_result_marks_id');
            }]);
        } else {
            $query->with('marks.subject');
        }

        if ($result_id) {
            return $query->find($result_id);
        }

        $query->whereHas('result', function ($result) use ($request) {
            $result->where('institution_id', $request->institution_id);
            $result->where('academic_session_id', $request->academic_session_id);
            $result->where('campus_id', $request->campus_id);
            $result->where('shift_id', $request->shift_id);
            $result->where('medium_id', $request->medium_id);
            $result->where('academic_class_id', $request->academic_class_id);
            $result->where('group_id', $request->group_id);
            $result->where('section_id', $request->section_id);
            $result->where('exam_id', $request->exam_id);
        });

        return $query->first();
    }

    /**
     * Get highest Marks
     */
    protected function getHighestMarks($model, $result, $table)
    {
        return $this->highestMarksQuery($model, $result, $table)
            ->where('rm.highest_marks', 1)
            ->pluck('rm.total_mark', 'rm.subject_id')
            ->toArray();
    }

    /**
     * query for highest marks
     */
    private function highestMarksQuery($model, $result, $table)
    {
        return $model::query()
            ->select('rm.id', 'rm.subject_id', 'rm.total_mark', 'rm.highest_marks', 'rd.student_id', 'rd.result_status')
            ->from("{$table}_result_marks as rm")
            ->join("{$table}_result_details as rd", 'rd.id', "rm.{$table}_result_details_id")
            ->join("{$table}_results as res", 'res.id', "rd.{$table}_result_id")
            ->where('res.institution_id', $result->institution_id)
            ->where('res.academic_session_id', $result->academic_session_id)
            ->where('res.campus_id', $result->campus_id)
            ->where('res.shift_id', $result->shift_id)
            ->where('res.medium_id', $result->medium_id)
            ->where('res.academic_class_id', $result->academic_class_id)
            ->where('res.group_id', $result->group_id)
            ->where('res.section_id', $result->section_id)
            ->where('res.exam_id', $result->exam_id);
    }
}
