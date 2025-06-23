<?php

/**
 * Dev: @OSHIT SUTRA DAR
 */

namespace App\Http\Controllers\Backend\Result\Secondary\Traits;

use App\Models\Result\SecondaryClassTestResultDetails;
use App\Models\Result\SecondaryClassTestResultMarks;
use App\Models\Result\SecondarySubjectAssign;

trait ClassTestResultTrait
{
    /**
     * Marks Format
     *
     * @return \array
     */
    protected function marksFormat($students, $subjectMarks)
    {
        $details = [];
        foreach ($students as $key => $student) {
            $arr = [
                'student' => [
                    'software_id' => $student['software_id'] ?? '',
                    'name_en' => $student['name_en'] ?? '',
                    'roll_number' => $student['profile']['roll_number'] ?? '',
                ],
                'student_id' => $student['id'],
                'total_mark' => 0,
                'result_status' => 'FAILED',
                'marks' => [[
                    'subject'      => [
                        'name_en' => $subjectMarks->subject->name_en ?? ''
                    ],
                    'subject_id'    => '',
                    'mark'          => '',
                    'exam_mark'     => $subjectMarks->ct_mark,
                    'pass_mark'     => $subjectMarks->ct_pass_mark,
                ]]
            ];
            array_push($details, $arr);
        }

        return $details;
    }

    /**
     * Result sync
     */
    private function resultSync($classTestResult)
    {
        $subjectAssign = SecondarySubjectAssign::with('details')->where([
            'institution_id'    => $classTestResult->institution_id,
            'medium_id'         => $classTestResult->medium_id,
            'academic_class_id' => $classTestResult->academic_class_id,
        ])->first();
        $subjectMarks = $subjectAssign->details->keyBy('subject_id');

        // dd($subjectMarks);
        $detailsArr = [];
        $marksArr = [];
        foreach ($classTestResult->details as $details) {

            // marks result status
            foreach ($details->marks as $markObj) {
                $subjectMark = $subjectMarks[$markObj->subject_id];
                $marksArr[] = [
                    'id'  => $markObj->id,
                    'result_status' => $subjectMark->ct_pass_mark >= (int) $markObj->mark ? 'FAILED' : 'PASSED'
                ];
            }

            // details result status
            $failedCheck = $details->marks->where('result_status', 'FAILED')->count();
            $totalMark = $details->marks->sum('mark');
            $detailsArr[] = [
                'id'            => $details->id,
                'total_mark'    => $totalMark,
                'result_status' => $failedCheck > 0 ? 'FAILED' : 'PASSED',
            ];
        }

        if ($marksArr) {
            SecondaryClassTestResultMarks::upsert($marksArr, ['id'], ['result_status']);
        }
        if ($detailsArr) {
            SecondaryClassTestResultDetails::upsert($detailsArr, ['id'], ['total_mark', 'result_status']);
        }
        return true;
    }
}
