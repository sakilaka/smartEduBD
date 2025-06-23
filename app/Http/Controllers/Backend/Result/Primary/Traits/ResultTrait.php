<?php

/**
 * Dev: @OSHIT SUTRA DAR
 */

namespace App\Http\Controllers\Backend\Result\Primary\Traits;

use App\Models\Result\PrimaryClassTestResultMarks;
use App\Models\Result\PrimaryGradeManagement;
use App\Models\Result\PrimaryResultDetails;
use App\Models\Result\PrimaryResultMarks;
use App\Models\Result\PrimarySubjectAssign;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

trait ResultTrait
{
    /**
     * Subject Wise ResultDetails
     *
     * @return \array
     */
    protected function subjectWiseStudent($resultID, $subject_id)
    {
        $resultData         = null;
        $pluckStudents      = [];

        $resultDetails = PrimaryResultDetails::with('student')
            ->whereSub('marks', 'subject_id', $subject_id)
            ->with(['marks' => function ($q) use ($subject_id) {
                $q->where('subject_id', $subject_id);
            }])
            ->where('primary_result_id', $resultID)
            ->get();

        if ($resultDetails->count() > 0) {
            $resultData =  $resultDetails->toArray();
            $pluckStudents = $resultDetails->pluck('student_id')->toArray();
        }

        return compact('resultData', 'pluckStudents');
    }

    /**
     * Marks Format
     *
     * @return \array
     */
    protected function marksFormat($students, $subjectMarks, $classTest)
    {
        $classTestDetails = $classTest->details ?? [];

        $details = [];
        foreach ($students as $key => $student) {
            $ctMark = '';
            if (!empty($classTestDetails)) {
                $classTestDetailsID = $classTest->details()->where('student_id', $student['id'])->value('id');
                $ctMark = PrimaryClassTestResultMarks::where('primary_class_test_result_details_id', $classTestDetailsID)
                    ->where('subject_id', $subjectMarks->subject_id)
                    ->value('mark');
            }

            $arr = [
                'student' => [
                    'software_id' => $student['software_id'] ?? '',
                    'name_en' => $student['name_en'] ?? '',
                    'roll_number' => $student['profile']['roll_number'] ?? '',
                ],
                'student_id' => $student['id'],
                'total_mark' => 0,
                'gpa' => 0,
                'letter_grade' => 'F',
                'result_status' => 'FAILED',
                'marks' => [[
                    'subject'      => [
                        'name_en' => $subjectMarks->subject->name_en ?? ''
                    ],
                    'subject_id'        => '',
                    'ct_mark'           => $ctMark,
                    'cq_mark'           => '',
                    'mcq_mark'          => '',
                    'obtained_mark'     => '',
                    'total_mark'        => '',
                    'gpa'               => '',
                    'letter_grade'      => ''
                ]]
            ];
            array_push($details, $arr);
        }

        return $details;
    }

    /**
     * Result marks sync
     */
    protected function resultMarksSync($result, $convert)
    {
        $gradeRanges = PrimaryGradeManagement::all();

        $subjectAssign = PrimarySubjectAssign::with('details')->where([
            'institution_id'    => $result->institution_id,
            'medium_id'         => $result->medium_id,
            'academic_class_id' => $result->academic_class_id,
        ])->first();
        $subjectMarks = $subjectAssign->details->keyBy('subject_id');
        $subject_sortings = $subjectAssign->details->pluck('sorting', 'subject_id')->toArray();

        $resultDetails = $result->details()
            ->with('marks', 'except_fourth_marks', 'fourth_marks')
            ->get()
            ->keyBy('student_id');

        $resultMarks = PrimaryResultMarks::whereIn('primary_result_details_id', $resultDetails->pluck('id'))
            ->get()
            ->keyBy('id');


        $marksArr = [];
        foreach ($resultDetails ?? [] as $detail) {
            foreach ($detail->marks ?? [] as $marks) {
                if (isset($resultMarks[$marks->id])) {
                    $markObj = $resultMarks->get($marks->id);
                    $subject_mark = $subjectMarks->get($markObj->subject_id);

                    $marksArr[] = $this->resultMarksGradeGpaCalculate(
                        $markObj,
                        $subject_mark,
                        $subject_sortings,
                        $gradeRanges,
                        $convert
                    );
                }
            }
        }

        // marks updated
        PrimaryResultMarks::upsert(
            $marksArr,
            ['id'],
            ['fourth_subject', 'sorting', 'obtained_mark', 'total_mark', 'gpa', 'letter_grade']
        );
    }

    /**
     * Result marks grade calculate
     */
    private function resultMarksGradeGpaCalculate($markObj,  $subjectMarks, $subject_sortings,  $gradeRanges, $convert = '')
    {
        $gradeGpa = [
            'id'            => $markObj->id,
            'obtained_mark' => 0,
            'total_mark'    => 0,
            'gpa'           => 0,
            'letter_grade'  => 'F',
            'fourth_subject' => null,
            'sorting'       => 0,
        ];

        if (!$subjectMarks) {
            return $gradeGpa;
        }

        // Check pass conditions
        $failConditions = [
            $subjectMarks->ct_pass_mark => $markObj->ct_mark,
            $subjectMarks->cq_pass_mark => $markObj->cq_mark,
            $subjectMarks->mcq_pass_mark => $markObj->mcq_mark,
        ];

        foreach ($failConditions as $passMark => $actualMark) {
            if (!empty($passMark) && (float) $actualMark < $passMark) {
                return $gradeGpa;
            }
        }

        // fourth subject && subject sorting
        $fourth_subject = ($subjectMarks->fourth_subject && $subjectMarks->subject_id == $markObj->subject_id) ? 1 : 0;
        $sorting = $subject_sortings[$markObj->subject_id] ?? 0;

        // Calculate total and obtained marks
        $examMark   = (int) $subjectMarks->total_mark;
        $obtained   = $markObj->ct_mark + $markObj->cq_mark + $markObj->mcq_mark;
        $totalMark  = ($obtained * 100) / $examMark;

        // Determine grade
        $gradeGpa = $gradeRanges->first(fn ($range) => $totalMark >= $range->from_mark && $totalMark <= $range->to_mark) ?? $gradeGpa;

        return [
            'id'            => $markObj->id,
            'obtained_mark' => round($totalMark),
            'total_mark'    => round($totalMark),
            'gpa'           => $gradeGpa['gpa'] ?? '0',
            'letter_grade'  => $gradeGpa['grade'] ?? 'F',
            'fourth_subject' => $fourth_subject,
            'sorting'       => $sorting,
        ];
    }

    /**
     * Result Details Sync
     */
    protected function resultDetailsSync($result)
    {
        $students = Student::select('id', 'software_id', 'name_en')
            ->where([
                'institution_id'    => $result->institution_id,
                'academic_session_id' => $result->academic_session_id,
                'campus_id'         => $result->campus_id,
                'shift_id'          => $result->shift_id,
                'medium_id'         => $result->medium_id,
                'group_id'          => $result->group_id,
                'section_id'        => $result->section_id,
                'academic_class_id' => $result->academic_class_id,
                'status'            => 'active',
            ])->get();

        $subjectAssign = PrimarySubjectAssign::where([
            'institution_id'    => $result->institution_id,
            'medium_id'         => $result->medium_id,
            'academic_class_id' => $result->academic_class_id,
        ])->first();

        $gradeRanges = PrimaryGradeManagement::all();

        $resultDetails = $result->details()
            ->with('marks', 'except_fourth_marks', 'fourth_marks')
            ->where('primary_result_id', $result->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $detailsToUpsert = [];
        foreach ($students as $student) {
            $resultDetail = $resultDetails->get($student->id) ?? null;
            if ($resultDetail) {
                $detailsToUpsert[] = $this->resultDetailsGradeGpaCalculate($resultDetail, $subjectAssign, $gradeRanges);
            }
        }

        if (!empty($detailsToUpsert)) {
            PrimaryResultDetails::upsert(
                $detailsToUpsert,
                ['id'],
                ['total_mark', 'result_status', 'letter_grade', 'gpa', 'total_mark_without_additional', 'gpa_without_additional']
            );
        }

        return true;
    }

    /**
     * Result Details GPA / Grade Calculate
     */
    private function resultDetailsGradeGpaCalculate($resultDetails, $subjectAssign, $gradeRanges)
    {
        $resultDetailsArr = [
            'id' => $resultDetails->id,
            'total_mark' => 0,
            'result_status' => 'FAILED',
            'letter_grade' => 'F',
            'gpa' => 0,
            'total_mark_without_additional' => 0,
            'gpa_without_additional' => 0
        ];

        $marks = $resultDetails->marks;
        if ($marks->isEmpty()) {
            return $resultDetailsArr;
        }

        $exceptFourthMarks = $resultDetails->except_fourth_marks;
        $totalMarkSubject = $exceptFourthMarks->count('subject_id');
        $totalAssignSubject = $subjectAssign->details->where('fourth_subject', '!=', 1)->count();

        // Calculate totals
        $additSubject = $resultDetails->fourth_marks;
        $addtTotalMark = $additSubject->total_mark ?? 0;
        $totalMark = $marks->sum('total_mark');
        $totalMarkWithoutAdditional = $totalMark - $addtTotalMark;

        $totalGpa = $exceptFourthMarks->sum('gpa');
        $totalSubject = $totalMarkSubject;

        $resultDetailsArr['total_mark'] = $totalMark;
        $resultDetailsArr['total_mark_without_additional'] = $totalMarkWithoutAdditional;

        // Check for failures or incomplete results
        $hasFailed = $exceptFourthMarks->where('letter_grade', 'F')->count();
        $hasBlankLG = $exceptFourthMarks->where('letter_grade', null)->count();
        if ($hasFailed > 0 || $hasBlankLG > 0 || $totalMarkSubject < $totalAssignSubject) {
            return $resultDetailsArr;
        }

        // Calculate GPA without additional subject
        $totalGpaWithoutAdditional = $totalSubject > 0 ? $totalGpa / $totalSubject : 0;

        // Calculate total GPA including additional subject
        $gpa = $totalGpaWithoutAdditional;
        $gradeManagement = $gradeRanges->first(fn ($range) => $gpa >= $range->from_gpa && $gpa <= $range->to_gpa) ?? [];

        return [
            'id'            => $resultDetails->id,
            'total_mark'    => round($totalMark),
            'result_status' => 'PASSED',
            'letter_grade'  => $gradeManagement['grade'] ?? '',
            'gpa'           => $gpa,
            'total_mark_without_additional' => round($totalMarkWithoutAdditional),
            'gpa_without_additional'        => round($totalGpaWithoutAdditional)
        ];
    }

    /**
     * Highest Marks Sync
     */
    protected function highestMarkSync($result)
    {
        // Reset the highest marks for all entries at once
        $this->highestMarksQuery($result)->update(['highest_marks' => 0]);

        // Fetch the highest mark item in one query
        $highestMarksIds = $this->highestMarksQuery($result)
            ->selectRaw('rm.id, rm.subject_id, rm.total_mark')
            ->whereRaw('rm.total_mark = (
                SELECT MAX(inner_rm.total_mark)
                FROM primary_result_marks AS inner_rm
                JOIN primary_result_details AS inner_rd ON inner_rd.id = inner_rm.primary_result_details_id
                JOIN primary_results AS inner_res ON inner_res.id = inner_rd.primary_result_id
                WHERE inner_rm.subject_id = rm.subject_id
                AND inner_res.institution_id = res.institution_id
                AND inner_res.academic_session_id = res.academic_session_id
                AND inner_res.campus_id = res.campus_id
                AND inner_res.shift_id = res.shift_id
                AND inner_res.medium_id = res.medium_id
                AND inner_res.academic_class_id = res.academic_class_id
                AND inner_res.group_id = res.group_id
                AND inner_res.section_id = res.section_id
                AND inner_res.exam_id = res.exam_id
            )')
            ->pluck('prm.id')
            ->toArray();

        PrimaryResultMarks::whereIn('id', $highestMarksIds)->update(['highest_marks' => 1]);
    }

    /**
     * Get highest Marks
     */
    protected function getHighestMarks($result)
    {
        return $this->highestMarksQuery($result)
            ->where('rm.highest_marks', 1)
            ->pluck('rm.total_mark', 'rm.subject_id')
            ->toArray();
    }

    /**
     * query for highest marks
     */
    private function highestMarksQuery($result)
    {
        return PrimaryResultMarks::query()
            ->select('rm.id', 'rm.subject_id', 'rm.total_mark', 'rm.highest_marks', 'rd.student_id', 'rd.result_status')
            ->from('primary_result_marks as rm')
            ->join('primary_result_details as rd', 'rd.id', 'rm.primary_result_details_id')
            ->join('primary_results as res', 'res.id', 'rd.primary_result_id')
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

    /**
     * Merit position sync
     */
    private function meritPositionSync($result, $type = 'merit_position_in_shift')
    {
        $query = PrimaryResultDetails::query()
            ->select('prd.id', 'prd.total_mark', 'prd.student_id')
            ->from('primary_result_details as prd')
            ->join('primary_results as pr', 'pr.id', 'prd.primary_result_id')
            ->join('students as std', 'prd.student_id', '=', 'std.id')
            ->join('student_profiles as profile', 'std.id', '=', 'profile.student_id')
            ->where('pr.institution_id', $result->institution_id)
            ->where('pr.academic_session_id', $result->academic_session_id)
            ->where('pr.campus_id', $result->campus_id)
            ->where('pr.exam_id', $result->exam_id)
            ->where('pr.medium_id', $result->medium_id)
            ->where('pr.group_id', $result->group_id)
            ->where('prd.result_status', 'PASSED')
            ->orderBy('prd.gpa', 'desc')
            ->orderBy('prd.total_mark_without_additional', 'desc')
            ->orderBy('profile.roll_number', 'asc');

        if ($type == 'merit_position_in_shift') {
            $query->where('pr.shift_id', $result->shift_id)
                ->where('pr.academic_class_id', $result->academic_class_id);
        }
        if ($type == 'merit_position_in_class') {
            $query->where('pr.academic_class_id', $result->academic_class_id);
        }
        if ($type == 'merit_position_in_section') {
            $query->where('pr.academic_class_id', $result->academic_class_id)
                ->where('pr.shift_id', $result->shift_id)
                ->where('pr.section_id', $result->section_id);
        }

        $result_details = $query->get();
        $ids = $result_details->pluck('id');

        // Bulk update merit positions to null
        PrimaryResultDetails::whereIn('id', $ids)->update([$type => null]);

        // Prepare data for bulk update of merit positions
        $updateData = [];
        foreach ($result_details as $key => $detail) {
            $updateData[] = [
                'id'   => $detail->id,
                $type  => $key + 1,
            ];
        }

        // Perform bulk update using upsert
        PrimaryResultDetails::upsert($updateData, ['id'], [$type]);
    }

    /**
     * Student mark entry sync
     */
    protected function studnentMarkEntry($result, $subject_id)
    {
        $pluckStudents = $this->subjectWiseStudent($result->id, $subject_id)['pluckStudents'];

        // Fetch students not already in the subject marks
        $students = Student::select('id', 'name_en', 'software_id')
            ->when($pluckStudents, fn ($q) => $q->whereNotIn('id', $pluckStudents))
            ->where([
                'institution_id'    => $result->institution_id,
                'academic_session_id' => $result->academic_session_id,
                'campus_id'         => $result->campus_id,
                'shift_id'          => $result->shift_id,
                'medium_id'         => $result->medium_id,
                'group_id'          => $result->group_id,
                'section_id'        => $result->section_id,
                'academic_class_id' => $result->academic_class_id,
                'status'            => 'active',
            ])
            ->get();

        if ($students->isEmpty()) {
            return;
        }

        // Prepare data for result details
        $resultDetailsData = $students->map(fn ($student) => [
            'primary_result_id' => $result->id,
            'student_id'          => $student->id,
        ])->toArray();
        PrimaryResultDetails::upsert($resultDetailsData, ['Primary_result_id', 'student_id']);

        // Fetch the updated result details for mark entries
        $resultDetails = PrimaryResultDetails::where('primary_result_id', $result->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Prepare data for marks
        $marksData = $resultDetails->map(fn ($detail) => [
            'primary_result_details_id' => $detail->id,
            'subject_id'                  => $subject_id,
            'updated_at'                  => now(),
            'created_at'                  => now(),
        ])->values()->toArray();
        PrimaryResultMarks::upsert($marksData, ['primary_result_details_id', 'subject_id']);
    }
}
