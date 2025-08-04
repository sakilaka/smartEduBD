<?php

/**
 * Dev: @OSHIT SUTRA DAR
 */

namespace App\Http\Controllers\Backend\Result\Secondary\Traits;

use App\Models\Result\SecondaryClassTestResultMarks;
use App\Models\Result\SecondaryGradeManagement;
use App\Models\Result\SecondaryResultDetails;
use App\Models\Result\SecondaryResultMarks;
use App\Models\Result\SecondarySubjectAssign;
use App\Models\Student;
use App\Models\StudentSubjectAssign;

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

        $resultDetails = SecondaryResultDetails::with('student')
            ->whereSub('marks', 'subject_id', $subject_id)
            ->with(['marks' => function ($q) use ($subject_id) {
                $q->where('subject_id', $subject_id);
            }])
            ->where('secondary_result_id', $resultID)
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
                $ctMark = SecondaryClassTestResultMarks::where('secondary_class_test_result_details_id', $classTestDetailsID)
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
                    'practical_mark'    => '',
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

    protected function resultMarksSync($result, $convert)
    {
        $gradeRanges = SecondaryGradeManagement::all();
        $isSpecialClass = in_array($result->academic_class_id, [8, 9, 10]);

        $subjectAssign = SecondarySubjectAssign::with('details')->where([
            'institution_id'    => $result->institution_id,
            'medium_id'         => $result->medium_id,
            'academic_class_id' => $result->academic_class_id,
            'academic_group_id' => $result->group_id,
        ])->first();


        if (!$subjectAssign || !$subjectAssign->details) {
            return false;
        }

        $subjectMarks = $subjectAssign->details->keyBy('subject_id');
        $subject_sortings = $subjectAssign->details->pluck('sorting', 'subject_id')->toArray();

        $resultDetails = $result->details()
            ->with('marks.combined_subject', 'except_fourth_marks', 'fourth_marks')
            ->get()
            ->keyBy('student_id');

        $resultMarks = SecondaryResultMarks::whereIn('secondary_result_details_id', $resultDetails->pluck('id'))
            ->get()
            ->keyBy('id');

        $marksArr = [];
        foreach ($resultDetails ?? [] as $detail) {
            $combinedMarks = [];

            foreach ($detail->marks ?? [] as $marks) {
                $subjectId = $marks->subject_id;

                if (in_array($subjectId, [20, 21])) {
                    $groupKey = '20_21';
                } elseif (in_array($subjectId, [22, 23])) {
                    $groupKey = '22_23';
                } else {
                    $groupKey = $subjectId;
                }

                if (!isset($combinedMarks[$groupKey])) {
                    $combinedMarks[$groupKey] = [];
                }
                $combinedMarks[$groupKey][] = $marks;
            }


            // Process each group
            foreach ($combinedMarks as $groupKey => $marksInGroup) {
                if (in_array($groupKey, ['20_21', '22_23']) && $isSpecialClass) {
                    // Special processing for combined subjects in classes 8,9,10
                    $totalObtained = 0;
                    $totalPossible = 0;
                    $markObjects = [];
                    $subjectMarkObjects = [];

                    foreach ($marksInGroup as $marks) {
                        $markObj = $resultMarks->get($marks->id);
                        $subject_mark = $subjectMarks->get($markObj->subject_id) ?? null;

                        if (!$markObj || !$subject_mark) {
                            continue;
                        }

                        $markObjects[] = $markObj;
                        $subjectMarkObjects[] = $subject_mark;

                        // Calculate obtained marks for this subject
                        $obtained = $markObj->cq_mark + $markObj->mcq_mark + $markObj->practical_mark;
                        $totalExamMark = $subject_mark->cq_mark + $subject_mark->mcq_mark + $subject_mark->practical_mark;

                        if ($convert) {
                            $convertMark = (int) ($subject_mark->converted_mark ?? 0);
                            $obtained = ($totalExamMark > 0) ? ($obtained / $totalExamMark) * $convertMark : 0;
                        }

                        $totalObtained += $obtained + $markObj->ct_mark;
                        $totalPossible += $subject_mark->cq_mark + $subject_mark->mcq_mark + $subject_mark->practical_mark + $subject_mark->ct_mark;
                    }

                    // Convert 150 marks to 100 scale for classes 8,9,10
                    $convertedPercentage = ($totalObtained / 150) * 100;

                    // Get grade based on converted percentage
                    $gradeGpa = $gradeRanges->first(fn($range) => $convertedPercentage >= $range->from_mark && $convertedPercentage <= $range->to_mark) ?? [
                        'gpa' => 0,
                        'grade' => 'F'
                    ];

                    // Update all marks in the group with the same grade
                    foreach ($markObjects as $index => $markObj) {
                        $subject_mark = $subjectMarkObjects[$index] ?? null;
                        $combined_marks = ($subject_mark && $subject_mark->combined_subject_id)
                            ? $detail->marks->firstWhere('subject_id', $subject_mark->combined_subject_id)
                            : null;

                        // Calculate obtained and total marks
                        $obtained = $markObj->cq_mark + $markObj->mcq_mark + $markObj->practical_mark;
                        $totalMark = $obtained + $markObj->ct_mark;

                        $marksArr[] = [
                            'id' => $markObj->id,
                            'combined_result_marks_id' => $combined_marks ? ($combined_marks->id ?? null) : null,
                            'obtained_mark' => round($obtained),
                            'total_mark' => round($totalMark),
                            'gpa' => $gradeGpa['gpa'] ?? '',
                            'letter_grade' => $gradeGpa['grade'] ?? '',
                            'fourth_subject' => ($subject_mark && $subject_mark->fourth_subject && $subject_mark->subject_id == $markObj->subject_id) ? 1 : 0,
                            'sorting' => $subject_sortings[$markObj->subject_id] ?? 0,
                            'ct_mark' => $markObj->ct_mark,
                            'cq_mark' => $markObj->cq_mark,
                        ];
                    }
                } elseif (in_array($groupKey, ['20_21', '22_23'])) {
                    // Combined subjects processing for other classes (not 8,9,10)
                    $totalObtained = 0;
                    $totalPossible = 0;
                    $markObjects = [];
                    $subjectMarkObjects = [];

                    // Special case for subject pair 20 and 21
                    $isSpecialCase = in_array($groupKey, ['20_21']);
                    $totalCQ = 0;
                    $totalMCQ = 0;

                    foreach ($marksInGroup as $marks) {
                        $markObj = $resultMarks->get($marks->id);
                        $subject_mark = $subjectMarks->get($markObj->subject_id) ?? null;

                        if (!$markObj || !$subject_mark) {
                            continue;
                        }

                        $markObjects[] = $markObj;
                        $subjectMarkObjects[] = $subject_mark;

                        // For special case subjects, collect CQ and MCQ marks
                        if ($isSpecialCase) {
                            $totalCQ += $markObj->cq_mark;
                            $totalMCQ += $markObj->mcq_mark;
                        }

                        // Calculate obtained marks for this subject
                        $obtained = $markObj->cq_mark + $markObj->mcq_mark + $markObj->practical_mark;
                        $totalExamMark = $subject_mark->cq_mark + $subject_mark->mcq_mark + $subject_mark->practical_mark;

                        if ($convert) {
                            $convertMark = (int) ($subject_mark->converted_mark ?? 0);
                            $obtained = ($totalExamMark > 0) ? ($obtained / $totalExamMark) * $convertMark : 0;
                        }

                        $totalObtained += $obtained + $markObj->ct_mark;
                        $totalPossible += $subject_mark->cq_mark + $subject_mark->mcq_mark + $subject_mark->practical_mark + $subject_mark->ct_mark;
                    }

                    // Check special conditions for subjects 20 and 21
                    $failedSpecialCondition = false;
                    if ($isSpecialCase) {
                        $failedSpecialCondition = ($totalCQ < 46) || ($totalMCQ < 20);
                    }

                    // Calculate average percentage if not failed by special condition
                    if (!$failedSpecialCondition) {
                        $averagePercentage = ($totalPossible > 0) ? ($totalObtained / $totalPossible) * 100 : 0;
                        $gradeGpa = $gradeRanges->first(fn($range) => $averagePercentage >= $range->from_mark && $averagePercentage <= $range->to_mark) ?? [
                            'gpa' => 0,
                            'grade' => 'F'
                        ];
                    } else {
                        // Failed by special condition
                        $gradeGpa = [
                            'gpa' => 0,
                            'grade' => 'F'
                        ];
                    }

                    // Update all marks in the group with the same grade
                    foreach ($markObjects as $index => $markObj) {
                        $subject_mark = $subjectMarkObjects[$index] ?? null;
                        $combined_marks = ($subject_mark && $subject_mark->combined_subject_id)
                            ? $detail->marks->firstWhere('subject_id', $subject_mark->combined_subject_id)
                            : null;

                        // Calculate obtained and total marks
                        $obtained = $markObj->cq_mark + $markObj->mcq_mark + $markObj->practical_mark;
                        $totalMark = $obtained + $markObj->ct_mark;

                        $marksArr[] = [
                            'id' => $markObj->id,
                            'combined_result_marks_id' => $combined_marks ? ($combined_marks->id ?? null) : null,
                            'obtained_mark' => round($obtained),
                            'total_mark' => round($totalMark),
                            'gpa' => $gradeGpa['gpa'] ?? '',
                            'letter_grade' => $gradeGpa['grade'] ?? '',
                            'fourth_subject' => ($subject_mark && $subject_mark->fourth_subject && $subject_mark->subject_id == $markObj->subject_id) ? 1 : 0,
                            'sorting' => $subject_sortings[$markObj->subject_id] ?? 0,
                            'ct_mark' => $markObj->ct_mark,
                            'cq_mark' => $markObj->cq_mark,
                        ];
                    }
                } else {
                    // Regular subject processing
                    foreach ($marksInGroup as $marks) {
                        if (isset($resultMarks[$marks->id])) {
                            $markObj = $resultMarks->get($marks->id);
                            $subject_mark = $subjectMarks->get($markObj->subject_id) ?? null;

                            if (!$subject_mark) {
                                continue;
                            }

                            $combined_marks = null;
                            if ($subject_mark->combined_subject_id ?? false) {
                                $combined_marks = $detail->marks->firstWhere('subject_id', $subject_mark->combined_subject_id);
                            }

                            // Calculate obtained and total marks
                            $obtained = $markObj->cq_mark + $markObj->mcq_mark + $markObj->practical_mark;
                            $originalTotalMark = $obtained + $markObj->ct_mark;
                            $totalMarkForGrading = $originalTotalMark;

                            // First condition: For subject_id 24, 39, 28, or 29, multiply by 2 for grading only
                            if ($markObj->subject_id == 24 || $markObj->subject_id == 39 || $markObj->subject_id == 28 || $markObj->subject_id == 29) {
                                $totalMarkForGrading = $originalTotalMark * 2;
                            }

                            // Second condition: For subject_id 28 or 29, check if class is 8,9,10
                            if (($markObj->subject_id == 28 || $markObj->subject_id == 29) && $isSpecialClass) {
                                $totalMarkForGrading = $originalTotalMark * 2;
                            } elseif ($markObj->subject_id == 28 || $markObj->subject_id == 29) {
                                // For other classes, use original mark for grading
                                $totalMarkForGrading = $originalTotalMark;
                            }

                            // Get grade based on totalMarkForGrading
                            $gradeGpa = $gradeRanges->first(
                                fn($range) =>
                                $totalMarkForGrading >= $range->from_mark && $totalMarkForGrading <= $range->to_mark
                            ) ?? ['gpa' => 0, 'grade' => 'F'];

                            $marksArr[] = [
                                'id' => $markObj->id,
                                'combined_result_marks_id' => $combined_marks ? ($combined_marks->id ?? null) : null,
                                'obtained_mark' => round($obtained),
                                'total_mark' => round($originalTotalMark), // Always save original total mark
                                'gpa' => $gradeGpa['gpa'] ?? 0,
                                'letter_grade' => $gradeGpa['grade'] ?? 'F',
                                'fourth_subject' => ($subject_mark->fourth_subject && $subject_mark->subject_id == $markObj->subject_id) ? 1 : 0,
                                'sorting' => $subject_sortings[$markObj->subject_id] ?? 0,
                                'ct_mark' => $markObj->ct_mark,
                                'cq_mark' => $markObj->cq_mark,
                            ];
                        }
                    }
                }
            }
        }

        // Save all updated marks
        SecondaryResultMarks::upsert(
            $marksArr,
            ['id'],
            [
                'fourth_subject',
                'sorting',
                'combined_result_marks_id',
                'obtained_mark',
                'total_mark',
                'gpa',
                'letter_grade',
                'ct_mark',
                'cq_mark',
            ]
        );

        return true;
    }

    private function resultMarksGradeGpaCalculate($markObj, $subjectMarks, $subject_sortings, $combined_marks, $gradeRanges, $convert = false)
    {
        $gradeGpa = [
            'id'            => $markObj->id,
            'obtained_mark' => 0,
            'total_mark'    => 0,
            'gpa'           => 0,
            'letter_grade'  => 'F',
            'combined_result_marks_id' => null,
            'fourth_subject' => null,
            'sorting'       => 0,

            'ct_mark' => 0,
            'cq_mark' => 0,
        ]; ########### 'ct_mark', 'cq_mark' removed when this 2024 result is published

        if (!$subjectMarks) {
            return $gradeGpa;
        }

        // Check pass conditions
        $failConditions = [
            $subjectMarks->ct_pass_mark => $markObj->ct_mark,
            $subjectMarks->cq_pass_mark => $markObj->cq_mark,
            $subjectMarks->mcq_pass_mark => $markObj->mcq_mark,
            $subjectMarks->practical_pass_mark => $markObj->practical_mark,
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
        $totalExamMark = $subjectMarks->cq_mark + $subjectMarks->mcq_mark + $subjectMarks->practical_mark;
        $convertMark = (int) $subjectMarks->converted_mark;
        $obtained = $markObj->cq_mark + $markObj->mcq_mark + $markObj->practical_mark;

        if ($convert) {
            $obtained = ($obtained / $totalExamMark) * $convertMark;
        }

        if ($convert) {
            $totalMark = $obtained + $markObj->ct_mark;
        } else {
            $totalMark = $markObj->obtained_mark ?? $obtained; ########### its removed when this 2024 result is published
            $obtained = $totalMark;
        }
        // Determine grade
        $gradeGpa = $gradeRanges->first(fn($range) => $totalMark >= $range->from_mark && $totalMark <= $range->to_mark) ?? $gradeGpa;


        ########### its removed when this 2024 result is published
        // CA MARK SET
        $ctMark = $convert ? $markObj->ct_mark : ((30 / 100) * $totalMark);
        $cqMark = $convert ? $markObj->cq_mark : ((70 / 100) * $totalMark);

        $ctMark = round($ctMark);
        $cqMark = round($cqMark);

        return [
            'id'                    => $markObj->id,
            'combined_result_marks_id' => $combined_marks['id'] ?? null,
            'obtained_mark'         => round($obtained),
            'total_mark'            => round($totalMark),
            'gpa'                   => $gradeGpa['gpa'] ?? '',
            'letter_grade'          => $gradeGpa['grade'] ?? '',
            'fourth_subject'        => $fourth_subject,
            'sorting'               => $sorting,

            'ct_mark' => $ctMark,
            'cq_mark' => $cqMark,
        ]; ########### 'ct_mark', 'cq_mark' removed when this 2024 result is published
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

        $subjectAssign = SecondarySubjectAssign::where([
            'institution_id'    => $result->institution_id,
            'medium_id'         => $result->medium_id,
            'academic_class_id' => $result->academic_class_id,
            'academic_group_id' => $result->group_id,
        ])->first();

        $gradeRanges = SecondaryGradeManagement::all();

        $resultDetails = $result->details()
            ->with('marks.combined_subject', 'except_fourth_marks', 'fourth_marks')
            ->where('secondary_result_id', $result->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $detailsToUpsert = [];
        $marksToUpsert = [];

        foreach ($students as $student) {
            $resultDetail = $resultDetails->get($student->id) ?? null;

            if ($resultDetail) {
                foreach ($resultDetail->marks ?? [] as $marks) {
                    // calculate for combined subject
                    if ($marks->combined_subject) {
                        $combined_mark = ($marks->total_mark + $marks->combined_subject->total_mark) / 200 * 100;
                        $gradeManagement = $gradeRanges->first(fn($range) => $combined_mark >= $range->from_mark && $combined_mark <= $range->to_mark) ?? [];

                        $marksToUpsert[] = [
                            'id' => $marks->id,
                            'combined_mark' => round($combined_mark),
                            'combined_gpa' => $gradeManagement['gpa'] ?? 0,
                            'combined_letter_grade' => $gradeManagement['grade'] ?? '',
                        ];
                    }
                }

                $detailsToUpsert[] = $this->resultDetailsGradeGpaCalculate($resultDetail, $subjectAssign, $gradeRanges);
            }
        }

        if (!empty($detailsToUpsert)) {
            SecondaryResultDetails::upsert(
                $detailsToUpsert,
                ['id'],
                [
                    'total_mark',
                    'result_status',
                    'letter_grade',
                    'gpa',
                    'total_mark_without_additional',
                    'gpa_without_additional'
                ]
            );
        }

        if (!empty($marksToUpsert)) {
            SecondaryResultMarks::upsert(
                $marksToUpsert,
                ['id'],
                ['combined_mark', 'combined_gpa', 'combined_letter_grade']
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
        $filteredExceptMarks = $exceptFourthMarks->filter(function ($mark) {
            return !in_array($mark->subject_id, [21, 23]);
        });

        $totalMarkSubject = $exceptFourthMarks->count('subject_id');
        $totalAssignSubject = $subjectAssign->details->where('fourth_subject', '!=', 1)->count();

        // Calculate totals
        $additSubject = $resultDetails->fourth_marks;
        $addtTotalMark = $additSubject->total_mark ?? 0;
        $totalMark = $marks->sum('total_mark');

        $totalMarkWithoutAdditional = $totalMark - $addtTotalMark;

        $totalGpa = $filteredExceptMarks->sum('gpa');
        $totalSubject = $totalMarkSubject - 2;

        $resultDetailsArr['total_mark'] = $totalMark;
        $resultDetailsArr['total_mark_without_additional'] = $totalMarkWithoutAdditional;

        // Check for failures or incomplete results
        $hasFailed = $exceptFourthMarks->where('letter_grade', 'F')->count();
        $hasBlankLG = $exceptFourthMarks->where('letter_grade', null)->count();

        if ($hasFailed > 0 || $hasBlankLG > 0 /* || $totalMarkSubject < $totalAssignSubject */) {
            return $resultDetailsArr;
        }

        // Calculate GPA without additional subject
        $totalGpaWithoutAdditional = $totalSubject > 0 ? $totalGpa / $totalSubject : 0;

        // Calculate total GPA including additional subject
        $additSubjectGpa = $additSubject->gpa ?? 0;
        $gpa = $totalGpaWithoutAdditional;

        if ($additSubjectGpa > 2) {
            $gpaAdjustment = $additSubjectGpa - 2;
            $gpa = $totalSubject > 0 ? ($totalGpa + $gpaAdjustment) / $totalSubject : 0;
            $gpa = min($gpa, 5);
        }
        $gradeManagement = $gradeRanges->first(fn($range) => $gpa >= $range->from_gpa && $gpa <= $range->to_gpa) ?? [];

        return [
            'id'            => $resultDetails->id,
            'total_mark'    => round($totalMark),
            'result_status' => 'PASSED',
            'letter_grade'  => $gradeManagement['grade'] ?? '',
            'gpa'           => $gpa,
            'total_mark_without_additional' => round($totalMarkWithoutAdditional),
            'gpa_without_additional' => round($totalGpaWithoutAdditional)
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
                FROM secondary_result_marks AS inner_rm
                JOIN secondary_result_details AS inner_rd ON inner_rd.id = inner_rm.secondary_result_details_id
                JOIN secondary_results AS inner_res ON inner_res.id = inner_rd.secondary_result_id
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
            ->pluck('rm.id')
            ->toArray();

        SecondaryResultMarks::whereIn('id', $highestMarksIds)->update(['highest_marks' => 1]);
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
        return SecondaryResultMarks::query()
            ->select('rm.id', 'rm.subject_id', 'rm.total_mark', 'rm.highest_marks', 'rd.student_id', 'rd.result_status')
            ->from('secondary_result_marks as rm')
            ->join('secondary_result_details as rd', 'rd.id', 'rm.secondary_result_details_id')
            ->join('secondary_results as res', 'res.id', 'rd.secondary_result_id')
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
        $query = SecondaryResultDetails::query()
            ->select('rd.id', 'rd.total_mark', 'rd.gpa', 'rd.student_id', 'rd.result_status', $type)
            ->from('secondary_result_details as rd')
            ->join('secondary_results as res', 'res.id', 'rd.secondary_result_id')
            ->join('students as std', 'rd.student_id', '=', 'std.id')
            ->join('student_profiles as profile', 'std.id', '=', 'profile.student_id')
            ->where('res.institution_id', $result->institution_id)
            ->where('res.academic_session_id', $result->academic_session_id)
            ->where('res.campus_id', $result->campus_id)
            ->where('res.exam_id', $result->exam_id)
            ->where('res.medium_id', $result->medium_id)
            ->where('res.group_id', $result->group_id)
            // ->orderBy('rd.gpa', 'desc')
            ->orderBy('rd.total_mark_without_additional', 'desc')
            ->orderBy('profile.roll_number', 'asc');

        if ($type == 'merit_position_in_shift') {
            $query->where('res.shift_id', $result->shift_id)
                ->where('res.academic_class_id', $result->academic_class_id);
        }
        if ($type == 'merit_position_in_class') {
            $query->where('res.academic_class_id', $result->academic_class_id);
        }
        if ($type == 'merit_position_in_section') {
            $query->where('res.academic_class_id', $result->academic_class_id)
                ->where('res.shift_id', $result->shift_id)
                ->where('res.section_id', $result->section_id);
        }

        $result_details = $query->get();
        $ids = $result_details->pluck('id');

        // Bulk update merit positions to null
        SecondaryResultDetails::whereIn('id', $ids)->update([$type => null]);

        // Re-sort based on total_marks (desc)
        $sorted_result_details = $result_details
            ->where('result_status', 'PASSED')
            ->sortByDesc(function ($item) {
                return [(float) $item->gpa, (float) $item->total_mark];
            })->values();
        // Prepare data for bulk update of merit positions
        $updateData = [];
        foreach ($sorted_result_details as $index => $detail) {
            $updateData[] = [
                'id'   => $detail->id,
                'total_mark' => $detail->total_mark,
                $type  => $index + 1,
            ];
        }

        // Perform bulk update using upsert
        SecondaryResultDetails::upsert($updateData, ['id'], [$type]);
    }

    /**
     * Student mark entry sync
     */
    protected function studnentMarkEntry($result, $subject_id)
    {
        $pluckStudents = $this->subjectWiseStudent($result->id, $subject_id)['pluckStudents'];

        // Fetch students not already in the subject marks
        $students = Student::select('id', 'name_en', 'software_id')
            ->when($pluckStudents, fn($q) => $q->whereNotIn('id', $pluckStudents))
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
        $resultDetailsData = $students->map(fn($student) => [
            'secondary_result_id' => $result->id,
            'student_id'          => $student->id,
        ])->toArray();
        SecondaryResultDetails::upsert($resultDetailsData, ['secondary_result_id', 'student_id']);

        // Fetch the updated result details for mark entries
        $resultDetails = SecondaryResultDetails::where('secondary_result_id', $result->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Prepare data for marks
        $marksData = $resultDetails->map(fn($detail) => [
            'secondary_result_details_id' => $detail->id,
            'subject_id'                  => $subject_id,
            'updated_at'                  => now(),
            'created_at'                  => now(),
        ])->values()->toArray();
        SecondaryResultMarks::upsert($marksData, ['secondary_result_details_id', 'subject_id']);
    }
}
