<?php

namespace App\Http\Controllers\Backend\Result\Primary;

use App\Http\Controllers\Backend\Result\Primary\Traits\ResultTrait;
use App\Http\Controllers\Controller;
use App\Models\MasterSetup\Institution;
use App\Models\Result\PrimaryResult;
use Illuminate\Http\Request;
use App\Models\Result\PrimaryResultDetails;
use App\Models\Result\PrimarySubjectAssign;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PrimaryResultReportController extends Controller
{
    use ResultTrait;

    /**
     * Result
     *
     * @return \Illuminate\Http\Response
     */
    public function result(Request $request)
    {
        $data['result'] = $request->all();
        $search['type'] = $request->type;
        $search['field_name'] = $request->field_name;
        $search['value'] = $request->search_keyword;

        $conditions = Student::commonArr($request);
        $conditions = array_merge($conditions, ['exam_id' => $request->exam_id]);

        $query = PrimaryResult::with(
            'academic_session',
            'institution',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
            'exam',
        );

        $query->with(['details' => function ($q) use ($search) {
            $q->select(
                'primary_result_details.*',
                'std.software_id',
                'std.name_en',
                'profile.roll_number'
            )->join('students as std', 'primary_result_details.student_id', '=', 'std.id')
                ->join('student_profiles as profile', 'std.id', '=', 'profile.student_id');

            $q->with(['marks' => function ($mq) {
                $mq->with('subject')->select('primary_result_details_id', 'subject_id')->where('letter_grade', 'F');
            }]);

            if (!empty($search['field_name']) && !empty($search['value'])) {
                $q->whereLike($search['field_name'], $search['value']);
            }

            if (in_array($search['type'],  [
                'merit_position_in_shift',
                'merit_position_in_section',
                'merit_position_in_class'
            ])) {
                $q->where('result_status', 'PASSED')
                    ->orderBy("primary_result_details.{$search['type']}", 'asc');
                // ->orderBy('primary_result_details.gpa', 'desc')
                // ->orderBy('total_mark_without_additional', 'desc')
                // ->orderBy('profile.roll_number', 'asc');
            } else  if ($search['type'] == 'unmerit') {
                $q->where('result_status', 'FAILED')->orderBy('total_mark_without_additional', 'asc')->orderBy('profile.roll_number', 'asc');
            } else {
                $q->orderBy('profile.roll_number', 'asc');
            }
        }]);

        $query->where($conditions);
        $result = $query->first();

        if (!empty($result)) {
            $data['result'] = $result;
            $data['excel_header'] = $this->excelHeader($result);
        }
        return $data;
    }

    /**
     * Class Wise Result
     *
     * @return \Illuminate\Http\Response
     */
    public function classwiseResult(Request $request)
    {
        $data['result'] = $request->all();
        $search['type'] = $request->type;
        $search['field_name'] = $request->field_name;
        $search['value'] = $request->search_keyword;

        $conditions = Student::commonArr($request);
        unset($conditions['section_id']);
        $conditions = array_merge($conditions, ['exam_id' => $request->exam_id]);

        $query = PrimaryResult::with(
            'academic_session',
            'institution',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'exam',
        );

        $query->with(['details' => function ($q) use ($search) {
            $q->select(
                'primary_result_details.*',
                'std.software_id',
                'std.name_en',
                'profile.roll_number',
                'sections.name as section_name'
            )->join('students as std', 'primary_result_details.student_id', '=', 'std.id')
                ->join('student_profiles as profile', 'std.id', '=', 'profile.student_id')
                ->join('primary_results as result', 'result.id', '=', 'primary_result_details.primary_result_id')
                ->join('sections', 'result.section_id', '=', 'sections.id');

            $q->with(['marks' => function ($mq) {
                $mq->with('subject')->select('primary_result_details_id', 'subject_id')->where('letter_grade', 'F');
            }]);

            if (!empty($search['field_name']) && !empty($search['value'])) {
                $q->whereLike($search['field_name'], $search['value']);
            }

            $q->where('result_status', 'PASSED')
                ->orderBy("primary_result_details.merit_position_in_class", 'asc');
        }]);

        $query->where($conditions);
        $result = $query->get();

        if ($result->isNotEmpty()) {
            $allDetails = $result->flatMap(function ($primaryResult) {
                return $primaryResult->details;
            });

            $sortedDetails = $allDetails->sortBy('merit_position_in_class')->values();

            $resultData = $result->first();
            $data['result'] = $resultData->setRelation('details', $sortedDetails);
        }
        return $data;
    }

    /**
     * Subject Wise Result
     *
     * @return \Illuminate\Http\Response
     */
    public function subjectwiseResult(Request $request)
    {
        $data['result'] = $request->all();
        $search['subject_id'] = $request->subject_id;
        $search['field_name'] = $request->field_name;
        $search['value'] = $request->search_keyword;

        $conditions = Student::commonArr($request);
        $conditions = array_merge($conditions, ['exam_id' => $request->exam_id]);

        $query = PrimaryResult::with(
            'academic_session',
            'institution',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
            'exam'
        );

        $query->with(['details' => function ($q) use ($search) {
            $q->select(
                'primary_result_details.id',
                'primary_result_details.primary_result_id',
                'primary_result_details.student_id',
                'rm.*',
                'std.id',
                'std.software_id',
                'std.name_en',
                'profile.roll_number'
            )
                ->join('students as std', 'primary_result_details.student_id', '=', 'std.id')
                ->join('student_profiles as profile', 'std.id', '=', 'profile.student_id')
                ->join('primary_result_marks as rm', 'primary_result_details.id', '=', 'rm.primary_result_details_id')
                ->where('rm.subject_id', $search['subject_id'])
                ->orderBy('profile.roll_number', 'asc');

            if (!empty($search['field_name']) && !empty($search['value'])) {
                $q->whereLike($search['field_name'], $search['value']);
            }
        }]);

        $query->where($conditions);
        $result = $query->first();

        if (!empty($result)) {
            $data['result'] = $result;
            $data['excel_header'] = $this->excelHeader($result);
        }

        return $data;
    }

    /**
     * Marksheet
     *
     * @return \Illuminate\Http\Response
     */
    public function marksheet($id)
    {
        return PrimaryResultDetails::with(
            'student',
            'marks.subject',
            'result.academic_session',
            'result.institution',
            'result.campus',
            'result.shift',
            'result.medium',
            'result.academic_class',
            'result.group',
            'result.section',
            'result.exam'
        )->find($id) ?? [];
    }

    /**
     * Download marksheet 
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadMarksheet($id)
    {
        $detail = PrimaryResultDetails::with(
            'student',
            'result.institution',
            'result.academic_session',
            'result.institution',
            'result.campus',
            'result.shift',
            'result.medium',
            'result.academic_class',
            'result.group',
            'result.section',
            'result.exam',
            'fourth_marks',
            'except_fourth_marks',
        )->find($id);

        $highest_marks = $this->getHighestMarks($detail->result);
        $storage_url = config('app.do_asset_url');

        $pdf      = Pdf::loadView('pdf.primary_marksheet', compact('detail', 'storage_url', 'highest_marks'))->setPaper('a4', 'landscape');
        $fileName = "marksheet.pdf";
        // return $pdf->download($fileName);
        return $pdf->stream();
    }

    /**
     * Download bulk marksheet 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadBulkMarksheet(Request $request)
    {
        $searchParams = json_decode($request->search_params, true);
        if (empty($searchParams)) return back();

        $type = $searchParams['type'] ?? '';

        $query = PrimaryResult::with(
            'academic_session',
            'institution',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
            'exam',
            'details.marks.subject',
        );

        $query->with(['details' => function ($q) use ($type) {
            $q->select(
                'primary_result_details.*',
                'std.software_id',
                'std.name_en',
                'profile.roll_number',
                'profile.fathers_name_en',
                'profile.mothers_name_en',
            )->join('students as std', 'primary_result_details.student_id', '=', 'std.id')
                ->join('student_profiles as profile', 'std.id', '=', 'profile.student_id');

            if (in_array($type,  [
                'merit_position_in_shift',
                'merit_position_in_section',
                'merit_position_in_class'
            ])) {
                $q->where('result_status', 'PASSED')
                    ->orderBy("primary_result_details.{$type}", 'asc');
                // ->orderBy('primary_result_details.gpa', 'desc')
                // ->orderBy('total_mark_without_additional', 'desc')
                // ->orderBy('profile.roll_number', 'asc');
            } else  if ($type == 'unmerit') {
                $q->where('result_status', 'FAILED')->orderBy('total_mark_without_additional', 'asc')->orderBy('profile.roll_number', 'asc');
            } else {
                $q->orderBy('profile.roll_number', 'asc');
            }
        }]);

        $query->where([
            'academic_session_id' => $searchParams['academic_session_id'],
            'institution_id' => $searchParams['institution_id'],
            'campus_id' => $searchParams['campus_id'],
            'shift_id' => $searchParams['shift_id'],
            'medium_id' => $searchParams['medium_id'],
            'academic_class_id' => $searchParams['academic_class_id'],
            'group_id' => $searchParams['group_id'],
            'section_id' => $searchParams['section_id'],
            'exam_id' => $searchParams['exam_id'],
        ]);
        $query->whereAny('type', $request->type);

        $result = $query->first();

        $highest_marks = $this->getHighestMarks($result);
        $storage_url = config('app.do_asset_url');

        $pdf      = Pdf::loadView('pdf.primary_marksheet_bulk', compact('result', 'storage_url', 'highest_marks'))->setPaper('a4', 'landscape');
        $fileName = "marksheet.pdf";
        // return $pdf->download($fileName);
        return $pdf->stream();
    }

    /**
     * Tabulation Sheet
     *
     * @return \Illuminate\Http\Response
     */
    public function tabulationSheet(Request $request)
    {
        $search['field_name'] = $request->field_name;
        $search['value'] = $request->search_keyword;

        $subjects = $this->subjectLists($request);

        $result = PrimaryResult::with(
            'academic_session',
            'institution',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
            'exam'
        )->where([
            'academic_session_id' => $request->academic_session_id,
            'institution_id' => $request->institution_id,
            'campus_id' => $request->campus_id,
            'shift_id' => $request->shift_id,
            'medium_id' => $request->medium_id,
            'academic_class_id' => $request->academic_class_id,
            'group_id' => $request->group_id,
            'section_id' => $request->section_id,
            'exam_id' => $request->exam_id,
        ])->first();

        $result_sheet = $this->tabulationFormat($result, $subjects, $search);

        $subjectChunks = array_chunk($subjects, 4);
        return [
            'result' => $result,
            'result_sheet' => $result_sheet,
            'subject_chunks' => $subjectChunks
        ];
    }

    private function tabulationFormat($result, $subjects, $search)
    {
        if (empty($result) || empty($result->details)) return false;

        // Fetch details
        $details = $result->details;
        if (!empty($search['field_name']) && !empty($search['value'])) {
            $details = $result->details()->whereSubLike('student', $search['field_name'], $search['value'])->get();
        }

        // Sort by roll number
        $details = $details->sortBy(function ($detail) {
            return $detail->student->roll_number;
        });

        // Chunk subjects into groups of 4
        $subjectChunks = array_chunk($subjects, 4);

        $result_sheet = [];

        foreach ($details ?? [] as $detail) {
            // Prepare student data
            $studentArr = [
                'name_en'       => $detail->student->name_en ?? '',
                'software_id'   => $detail->student->software_id ?? '',
                'roll_number'   => $detail->student->roll_number ?? '',
                'total_mark'    => $detail->total_mark,
                'gpa'           => $detail->gpa,
                'letter_grade'  => $detail->letter_grade,
                'result_status' => $detail->result_status,
                'subject_chunks' => []
            ];

            // Process each subject chunk
            foreach ($subjectChunks as $chunk) {
                $chunkMarks = [];
                foreach ($chunk as $sub) {
                    $mark = $detail->marks()->with('subject')->where('subject_id', $sub['subject_id'])->first();
                    $chunkMarks[] = !empty($mark) ? $mark->toArray() : [];
                }

                // Add the chunk only if it contains marks
                if (!empty($chunkMarks)) {
                    $studentArr['subject_chunks'][] = $chunkMarks;
                }
            }

            // Add the student to the result sheet
            $result_sheet[] = $studentArr;
        }

        return $result_sheet;
    }


    // Tabulation format
    private function __tabulationFormat($result, $subjects, $search)
    {
        if (empty($result) && empty($result->details)) return false;

        $details = $result->details;
        if (!empty($search['field_name']) && !empty($search['value'])) {
            $details = $result->details()->whereSubLike('student', $search['field_name'], $search['value'])->get();
        }

        $result_sheet = [];
        foreach ($details ?? [] as $key => $detail) {
            $studentArr = [
                'name_en'       => $detail->student->name_en ?? '',
                'software_id'   => $detail->student->software_id ?? '',
                'roll_number'  => $detail->student->profile->roll_number ?? '',
                'total_mark'    => $detail->total_mark,
                'total_mark_without_additional'    => $detail->total_mark_without_additional,
                'gpa'           => $detail->gpa,
                'letter_grade'  => $detail->letter_grade,
                'result_status' => $detail->result_status,
                'subjects'      => []
            ];

            foreach ($subjects as $key => $sub) {
                $mark = $detail->marks()->with('subject')->where('subject_id', $sub['subject_id'])->first();
                $subject_mark = !empty($mark) ? $mark->toArray() : [];

                array_push($studentArr['subjects'], $subject_mark);
            }

            array_push($result_sheet, $studentArr);
        }
        return $result_sheet;
    }

    // subject lists
    private function subjectLists($request)
    {
        $subjects = [];
        $subjectAssign = PrimarySubjectAssign::where([
            'institution_id'    => $request->institution_id,
            'medium_id'         => $request->medium_id,
            'academic_class_id' => $request->academic_class_id,
        ])->first();

        if (!empty($subjectAssign)) {
            $subjects = $subjectAssign->details()
                ->select('primary_subject_assign_id', 'subject_id')
                ->with('subject')->get()->toArray() ?? [];
        }

        return $subjects;
    }

    /**
     * Grade Summary
     *
     * @return \Illuminate\Http\Response
     */
    public function gradeSummary(Request $request)
    {
        $result = PrimaryResult::with(
            'academic_session',
            'institution',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
            'exam'
        )
            ->where([
                'academic_session_id' => $request->academic_session_id,
                'institution_id' => $request->institution_id,
                'campus_id' => $request->campus_id,
                'shift_id' => $request->shift_id,
                'medium_id' => $request->medium_id,
                'academic_class_id' => $request->academic_class_id,
                'group_id' => $request->group_id,
                'section_id' => $request->section_id,
                'exam_id' => $request->exam_id,
            ])->first();

        $subject_summary = DB::table('primary_result_marks as rm')
            ->selectRaw('
                    rm.subject_id,
                    sub.name_en as subject_name,
                    rm.letter_grade,
                    COUNT(rm.id) as grade_count
                ')
            ->join('primary_result_details as rd', 'rm.primary_result_details_id', '=', 'rd.id')
            ->join('subjects as sub', 'rm.subject_id', '=', 'sub.id')
            ->where('rd.primary_result_id', $result->id)
            ->orderBy('letter_grade', 'asc')
            ->groupBy('rm.subject_id', 'rm.letter_grade')
            ->get()
            ->groupBy('subject_id');


        $grade_summary = $result->details()->selectRaw('letter_grade, COUNT(id) as grade_count')->groupBy('letter_grade')->get();

        return [
            'result' => $result,
            'grade_summary' => $grade_summary,
            'subject_summary' => $subject_summary,
        ];
    }

    /**
     * Number Sheet
     *
     * @return \Illuminate\Http\Response
     */
    public function numberSheet(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;
        $cond = [
            'academic_session_id' => $request->academic_session_id,
            'institution_id' => $institution_id,
            'campus_id' => $request->campus_id,
            'shift_id' => $request->shift_id,
            'medium_id' => $request->medium_id,
            'academic_class_id' => $request->academic_class_id,
            'group_id' => $request->group_id,
            'section_id' => $request->section_id,
        ];


        $data['students'] = Student::select('students.id', 'software_id', 'name_en')
            ->with('profile:student_id,roll_number')
            ->where($cond)
            ->where([
                'status' => 'active',
            ])
            ->join('student_profiles as sp', 'sp.student_id', '=', 'students.id')
            ->orderBy('sp.roll_number', 'asc')
            ->get();

        $data['result'] = PrimaryResult::with(
            'academic_session',
            'institution',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
            'exam'
        )->where([
            'academic_session_id' => $request->academic_session_id,
            'exam_id' => $request->exam_id,
        ])->where($cond)->first();

        return $data;
    }

    /**
     * Excel Header
     *
     * @param $result
     * @return void
     */
    private function excelHeader($result)
    {
        $institution = $result->institution->name ?? '';
        $session = $result->academic_session->name ?? '';
        $campus = $result->campus->name ?? '';
        $shift = $result->shift->name ?? '';
        $className = $result->academic_class->name ?? '';
        $section = $result->section->name ?? '';
        $group = $result->group->name ?? '';
        $exam = $result->exam->name ?? '';

        $excel_header = [
            "Institution: {$institution}",
            "Session: {$session}",
            "Campus: {$campus}",
            "Shift: {$shift}",
            "Academic Info: {$className} - {$group} ({$section})",
            "Exam Name: {$exam}",
        ];
        return $excel_header;
    }
}