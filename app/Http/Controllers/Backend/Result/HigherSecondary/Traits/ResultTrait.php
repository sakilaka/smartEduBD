<?php

/**
 * Dev: @OSHIT SUTRA DAR
 */

namespace App\Http\Controllers\Backend\Result\HigherSecondary\Traits;

use App\Models\ClassTestResult;
use App\Models\ClassTestResultMarks;
use App\Models\GradeManagement;
use App\Models\MasterSetup\Exam;
use App\Models\MasterSetup\Subject as MasterSetupSubject;
use App\Models\Result\HigherSecondaryResultDetails;
use App\Models\Result\HigherSecondaryResultMarks;
use App\Models\Result\HigherSecondarySubjectAssign;
use App\Models\Result\SecondaryGradeManagement;
use App\Models\ResultDetails;
use App\Models\ResultMarks;
use App\Models\Student;
use App\Models\StudentSubjectAssign;
use App\Models\Subject;
use App\Models\SubjectAssign;
use Illuminate\Support\Facades\Log;

trait ResultTrait
{
    /**
     * Marks Format
     *
     * @return \array
     */
    protected function marksFormat($students, $subjectMarks, $classTest)
    {
        // dd($students);
        $details = [];
        $subjectId = $subjectMarks->subject_id;
        // dd($subjectId);

        $studentIds = array_column($students, 'id');
        $classTestDetailsIDs = [];
        if ($classTest) {
            $classTestDetailsIDs = $classTest->details()
                ->whereIn('student_id', $studentIds)
                ->pluck('id', 'student_id');
        }

        $marks = ClassTestResultMarks::whereIn('class_test_result_details_id', $classTestDetailsIDs)
            ->where('subject_id', $subjectId)
            ->get()
            ->keyBy('class_test_result_details_id');

        foreach ($students as $student) {
            $ctMark = '';
            $classTestDetailsID = $classTestDetailsIDs[$student['id']] ?? null;

            if ($classTestDetailsID) {
                $ctMark = $marks[$classTestDetailsID]->mark ?? '';
            }

            $arr = [
                'student' => [
                    'student_id' => $student['student_id'] ?? '',
                    'name' => $student['name'] ?? '',
                    'college_roll' => $student['college_roll'] ?? '',
                ],
                'student_id' => $student['id'],
                'total_mark' => 0,
                'gpa' => 0,
                'letter_grade' => 0,
                'result_status' => 'FAILED',
                'marks' => [[
                    'subject'      => [
                        'name_en' => $subjectMarks->subject->name_en ?? ''
                    ],
                    'subject_id'        => $subjectId,
                    'ct_mark'           => $ctMark,
                    'cq_mark'           => '',
                    'mcq_mark'          => '',
                    'practical_mark'    => '',
                    'obtained_mark'     => '',
                    'total_mark'        => '',
                    'gpa'               => '',
                    'letter_grade'      => '',
                    'is_absent'         => 0
                ]]
            ];
            $details[] = $arr;
        }

        return $details;
    }

    private function resultGradeGpaUpdate($resultDetails, $result)
    {
        if ($resultDetails->marks()->count() == 0) {
            return false;
        }

        $totalGpaWithoutAdditional = 0;
        $additSubject = $resultDetails->marks()->where('additional_subject', 1)->first();
        $totalMark = $resultDetails->marks()->sum('total_mark');
        $totalGpa = $resultDetails->marks()->where('additional_subject', '!=', 1)->sum('gpa');
        $totalSubject = $resultDetails->marks()->where('additional_subject', '!=', 1)->count('subject_id');

        $addt_total_mark = $additSubject->total_mark ?? 0;
        $totalMarkWithoutAdditional =  $totalMark - $addt_total_mark;

        // failed check
        $updateArr = [
            'total_mark'    => $totalMark,
            'result_status' => 'FAILED',
            'letter_grade'  => 'F',
            'gpa'           => 0,
            'total_mark_without_additional' => 0,
            'gpa_without_additional'        => 0
        ];

        $failedCheck = $resultDetails->marks()->where('additional_subject', '!=', 1)->where('letter_grade', 'F')->first();
        $failedCheckGradeEmpty = $resultDetails->marks()->where('additional_subject', '!=', 1)->where('letter_grade', null)->first();
        if (!empty($failedCheck)) {
            $resultDetails->update($updateArr);
            return true;
        } elseif (!empty($failedCheckGradeEmpty)) {
            $resultDetails->update($updateArr);
            return true;
        }

        if ($totalGpa > 0 && $totalSubject > 0) {
            $totalGpaWithoutAdditional = $totalGpa / $totalSubject;
        }

        // total gpa
        $addit_subject_gpa = $additSubject->gpa ?? 0;
        if ($addit_subject_gpa > 2) {
            $gpa = 0;
            if ($totalSubject > 0) {
                $addGpa = $addit_subject_gpa - 2;
                $gpa = ($totalGpa + $addGpa) / $totalSubject;
                $gpa = ($gpa > 5) ? 5 : $gpa;
            }
        } else {
            $gpa = $totalGpaWithoutAdditional;
        }

        $totalSubWithAddt = $resultDetails->marks()->count('subject_id');
        $letterGrade = SecondaryGradeManagement::where('from_gpa', '<=', $gpa)->where('to_gpa', '>=', $gpa)->value('grade');

        if ($totalSubWithAddt >= $result->total_exam_subjects) {
            $updateArr = [
                'total_mark'    => $totalMark,
                'result_status' => 'PASSED',
                'letter_grade'  => $letterGrade,
                'gpa'           => $gpa,
                'total_mark_without_additional' => $totalMarkWithoutAdditional,
                'gpa_without_additional'        => $totalGpaWithoutAdditional
            ];
        }

        $resultDetails->update($updateArr);
        return true;
    }


    protected function resultMarksSync($result, $convert)
    {
        $subjects = HigherSecondarySubjectAssign::where([
            'academic_qualification_id' => $result->academic_qualification_id,
            'department_id' => $result->department_id,
            'academic_class_id' => $result->academic_class_id,
        ])->first();

        // Get Class Test Details
        $exam = Exam::find($result->exam_id);
        $classTestId = $exam->class_test_exam_id;

        $classTest = ClassTestResult::where([
            'academic_session_id' => $result->academic_session_id,
            'department_id' => $result->department_id,
            'academic_qualification_id' => $result->academic_qualification_id,
            'academic_class_id' => $result->academic_class_id,
            'exam_id' => $classTestId,
            'status' => 'published',
        ])->first();

        $classTestMarks = [];
        $classTestDetails = [];

        if ($classTest) {
            $studentIds = $result->details->pluck('student_id')->toArray();
            $classTestDetails = $classTest->details()
                ->whereIn('student_id', $studentIds)
                ->pluck('id', 'student_id');

            $classTestMarks = ClassTestResultMarks::whereIn('class_test_result_details_id', $classTestDetails)
                ->get()
                ->groupBy(function ($item) {
                    return $item->class_test_result_details_id . '-' . $item->subject_id;
                });
        }

        foreach ($result->details ?? [] as $detail) {
            foreach ($detail->marks ?? [] as $markObj) {
                $subjectId = $markObj->subject_id;
                $ct_mark   = $markObj->ct_mark;

                $classTestDetailsID = $classTestDetails[$detail->student_id] ?? null;
                $key = $classTestDetailsID . '-' . $subjectId;
                if ($classTestDetailsID && isset($classTestMarks[$key])) {
                    $ct_mark = $classTestMarks[$key]->first()->mark ?? 0;
                }

                $obtained   = $markObj->cq_mark + $markObj->mcq_mark + $markObj->practical_mark;
                $calMark    = $convert ? $obtained * 80 / 100 : $obtained;
                $totalMark  = $calMark + $ct_mark;

                $gradeManagement = $this->gradeCalculate($subjects, $markObj, $totalMark);

                $arr = [
                    'ct_mark'       => $ct_mark,
                    'obtained_mark' => number_format($calMark, 2),
                    'total_mark'    => number_format($totalMark, 2),
                    'gpa'           => $gradeManagement['gpa'] ?? '0',
                    'letter_grade'  => $gradeManagement['grade'] ?? 'F',
                ];
                $markObj->update($arr);
            }
        }
        return true;
    }

    /**
     * Result Grade Sync
     */
    protected function resultGradeSync($result)
    {
        $students = Student::select('id', 'student_id', 'name', 'college_roll')
            ->where([
                'academic_session_id'       => $result->academic_session_id,
                'department_id'             => $result->department_id,
                'academic_qualification_id' => $result->academic_qualification_id,
                'academic_class_id'         => $result->academic_class_id,
                'status'                    => 'active',
            ])->get();

        $subjectAssign = HigherSecondarySubjectAssign::where([
            'department_id' => $result->department_id,
            'academic_class_id' => $result->academic_class_id,
        ])->first();
        $subject_sortings = $subjectAssign->details()->pluck('sorting', 'subject_id')->toArray();

        foreach ($students as $student) {
            $resultDetails = $result->details()->where(['result_id' => $result->id, 'student_id' => $student->id])->first();

            if ($resultDetails) {

                // student subject assign
                $additionalSubject = StudentSubjectAssign::where([
                    'department_id' => $result->department_id,
                    'academic_class_id' => $result->academic_class_id,
                    'student_id' => $student->id,
                    'main_subject' => 0
                ])->value('subject_id');

                foreach ($resultDetails->marks ?? [] as $marks) {
                    $marks->update([
                        'additional_subject' => $additionalSubject == $marks->subject_id ? 1 : 0,
                        'sorting' => $subject_sortings[$marks->subject_id] ?? 0
                    ]);
                }

                $this->resultGradeGpaUpdate($resultDetails, $result);
            }
        }

        return true;
    }

    /**
     * Grade Calculate
     */
    private function gradeCalculate($subjects, $markObj, $totalMark)
    {
        $gradeGpa = ['gpa' => 0, 'grade' => 'F',];
        $subjectMarks = $subjects->details()->where('subject_id', $markObj->subject_id)->first();

        if (!empty($subjectMarks->ct_pass_mark) && (float) $markObj->ct_mark < $subjectMarks->ct_pass_mark) {
            return $gradeGpa;
        } else  if (!empty($subjectMarks->cq_pass_mark) && (float) $markObj->cq_mark < $subjectMarks->cq_pass_mark) {
            return $gradeGpa;
        } else  if (!empty($subjectMarks->mcq_pass_mark) && (float) $markObj->mcq_mark < $subjectMarks->mcq_pass_mark) {
            return $gradeGpa;
        } else  if (!empty($subjectMarks->practical_pass_mark) && (float) $markObj->practical_mark < $subjectMarks->practical_pass_mark) {
            return $gradeGpa;
        }

        $gradeManagement = SecondaryGradeManagement::where('from_mark', '<=', $totalMark)->where('to_mark', '>=', $totalMark)->first();
        return $gradeManagement;
    }


    /**
     * Merit position sync
     */
    private function meritPositionSync($result, $type = 'merit_position_in_department')
    {
        $query = HigherSecondaryResultDetails::query()
            ->select('rd.id', 'rd.total_mark', 'rd.student_id')
            ->from('result_details as rd')
            ->join('results as rs', 'rs.id', 'rd.result_id')
            ->where('rs.academic_session_id', $result->academic_session_id)
            ->where('rs.academic_qualification_id', $result->academic_qualification_id)
            ->where('rs.exam_id', $result->exam_id)
            ->orderBy('rd.total_mark', 'desc');

        if ($type === 'merit_position_in_department' || $type === 'merit_position_in_class') {
            $query->where('rs.department_id', $result->department_id);
        }
        if ($type === 'merit_position_in_class') {
            $query->where('rs.academic_class_id', $result->academic_class_id);
        }

        $result_details = $query->get();

        foreach ($result_details ?? [] as $key => $detail) {
            $detail->update([$type => $key + 1]);
        }
    }

    protected function studnentMarkEntry($result, $subject_id)
    {
        Log::info('--- [Step 1] Starting studentMarkEntry ---', ['subject_id' => $subject_id, 'result_id' => $result->id]);

        $studentSubjectAssign = [];

        // Step 2: Get SubjectAssign
        $subjects = HigherSecondarySubjectAssign::where([
            'academic_qualification_id' => $result->academic_qualification_id,
            'department_id' => $result->department_id,
            'academic_class_id' => $result->academic_class_id,
        ])->first();

        Log::info('[Step 2] Fetched SubjectAssign', ['subject_assign_id' => optional($subjects)->id]);

        // Step 3: Check if it's a common subject
        $common_subject = $subjects->details()->where('subject_id', $subject_id)
            ->with('subject')
            ->where('main_subject', 0)
            ->where('fourth_subject', 0)
            ->exists();

        Log::info('[Step 3] Is common subject?', ['common_subject' => $common_subject]);

        // Step 4: If not common, get studentSubjectAssign data
        if (!$common_subject) {
            $studentSubjectAssign = StudentSubjectAssign::where([
                'department_id' => $result->department_id,
                'academic_class_id' => $result->academic_class_id,
            ])
                ->whereIn('student_id', $result->details->pluck('student_id')->toArray())
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->subject_id . '_' . $item->student_id => $item->subject_id];
                })
                ->toArray();

            Log::info('[Step 4] studentSubjectAssign prepared', ['assignments' => $studentSubjectAssign]);
        }

        // Step 5: Get pluckStudents from subjectWiseStudent()
        $pluckStudents = $this->subjectWiseStudent($result->id, $subject_id)['pluckStudents'];
        Log::info('[Step 5] Plucked existing students with marks', ['pluckStudents' => $pluckStudents]);

        // Step 6: Fetch students who need mark entry
        $students = Student::select('id', 'name', 'student_id')
            ->where([
                'academic_session_id' => $result->academic_session_id,
                'department_id' => $result->department_id,
                'academic_qualification_id' => $result->academic_qualification_id,
                'academic_class_id' => $result->academic_class_id,
                'status' => 'active',
            ])
            ->whereHas('subjects', function ($q) use ($subject_id, $common_subject, $result) {
                if (!$common_subject) {
                    $q->where([
                        'subject_id' => $subject_id,
                        'department_id' => $result->department_id,
                        'academic_class_id' => $result->academic_class_id,
                    ]);
                }
            })
            ->get();


        Log::info('[Step 6] Students found for mark entry', ['count' => $students->count(), 'student_ids' => $students->pluck('id')]);

        if ($students->isEmpty()) {
            Log::info('[Step 6] No new students found for mark entry');
            return;
        }

        // Step 7: Insert result_details if not already there
        $resultDetailsData = $students->map(fn($student) => [
            'result_id' => $result->id,
            'student_id' => $student->id,
        ])->toArray();

        Log::info('[Step 7] Upserting ResultDetails', ['data' => $resultDetailsData]);

        HigherSecondaryResultDetails::upsert($resultDetailsData, ['result_id', 'student_id']);

        // Step 8: Get result details for mark entry
        $resultDetails = HigherSecondaryResultDetails::where('result_id', $result->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        Log::info('[Step 8] Loaded ResultDetails', ['count' => $resultDetails->count()]);

        // Step 9: Check for child subjects
        $childStudentOrMarkData = MasterSetupSubject::where('parent_id', $subject_id)->pluck('id');
        Log::info('[Step 9] Checking for child subjects', ['child_subject_ids' => $childStudentOrMarkData]);



        if ($childStudentOrMarkData->count() > 0) {
            $subjects = HigherSecondarySubjectAssign::where([
                'academic_qualification_id' => $result->academic_qualification_id,
                'department_id'             => $result->department_id,
                'academic_class_id'         => $result->academic_class_id,
            ])->first();

            foreach ($resultDetails as $detail) {
                $marks = HigherSecondaryResultMarks::whereIn('subject_id', $childStudentOrMarkData)
                    ->where('result_details_id', $detail->id)
                    ->get();

                if ($marks->count() > 0) {
                    $total = $marks->sum('total_mark');
                    $totalCt = $marks->sum('ct_mark');
                    $totalCq = $marks->sum('cq_mark');
                    $totalMcq = $marks->sum('mcq_mark');
                    $totalPrac = $marks->sum('practical_mark');

                    $average = round($total / $marks->count(), 2);
                    $ct_mark = round($totalCt / $marks->count(), 2);
                    $cq_mark = round($totalCq / $marks->count(), 2);
                    $mcq_mark = round($totalMcq / $marks->count(), 2);
                    $prac_mark = round($totalPrac / $marks->count(), 2);

                    // Create a mock mark object for gradeCalculate (if needed)
                    $markObj = new \stdClass();
                    $markObj->subject_id = $subject_id;
                    $markObj->ct_mark = $ct_mark;
                    $markObj->cq_mark = $mcq_mark;
                    $markObj->mcq_mark = $mcq_mark;
                    $markObj->practical_mark = $prac_mark;

                    // Call grade calculation
                    $gradeData = $this->gradeCalculate($subjects, $markObj, $average);
                    // dd($gradeData);
                    $gpa = $gradeData->gpa ?? 0;
                    $grade = $gradeData->grade ?? 'F';

                    HigherSecondaryResultMarks::updateOrCreate(
                        [
                            'result_details_id' => $detail->id,
                            'subject_id'        => $subject_id
                        ],
                        [
                            'cq_mark'           => $cq_mark,
                            'mcq_mark'          => $mcq_mark,
                            'practical_mark'    => $prac_mark,
                            'total_mark'        => $average,
                            'obtained_mark'     => $average,
                            'gpa'               => $gpa,
                            'letter_grade'      => $grade,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]
                    );
                }
            }

            return;
        }


        // Step 10: Final mark entry
        $marksData = $resultDetails->map(function ($detail) use ($subject_id, $common_subject, $studentSubjectAssign) {
            $studentSubExists = $studentSubjectAssign[$subject_id . '_' . $detail->student_id] ?? null;

            if ($common_subject || $studentSubExists == $subject_id) {
                return [
                    'result_details_id' => $detail->id,
                    'subject_id' => $subject_id,
                    'created_at' => now(),
                ];
            }
            return null;
        })->filter()->values()->toArray();

        Log::info('[Step 10] Upserting ResultMarks', ['marks_count' => count($marksData)]);

        HigherSecondaryResultMarks::upsert($marksData, ['result_details_id', 'subject_id']);

        Log::info('--- [Step 11] studentMarkEntry Completed ---');
    }


    protected function subjectWiseStudent($resultID, $subject_id)
    {
        $resultData         = null;
        $pluckStudents      = [];

        $resultDetails = HigherSecondaryResultDetails::with('student')
            ->whereSub('marks', 'subject_id', $subject_id)
            ->with(['marks' => function ($q) use ($subject_id) {
                $q->where('subject_id', $subject_id);
            }])
            ->where('result_id', $resultID)
            ->get();

        if ($resultDetails->count() > 0) {
            $resultData =  $resultDetails->toArray();
            $pluckStudents = $resultDetails->pluck('student_id')->toArray();
        }

        return compact('resultData', 'pluckStudents');
    }
}
