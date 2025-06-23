<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Result\Secondary;

use App\Http\Resources\Resource;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Result\Secondary\Traits\ClassTestResultTrait;
use App\Models\MasterSetup\Institution;
use App\Models\Result\SecondaryClassTestResult;
use App\Models\Result\SecondaryClassTestResultDetails;
use App\Models\Result\SecondarySubjectAssign;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class SecondaryClassTestResultController extends Controller
{
    use ClassTestResultTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $query = SecondaryClassTestResult::latest('id')->with(
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
        $query->whereLike($request->field_name, $request->value);
        $query->whereAny('academic_session_id', $request->academic_session_id);
        $query->whereAny('institution_id', $institution_id);
        $query->whereAny('campus_id', $request->campus_id);
        $query->whereAny('shift_id', $request->shift_id);
        $query->whereAny('medium_id', $request->medium_id);
        $query->whereAny('academic_class_id', $request->academic_class_id);
        $query->whereAny('group_id', $request->group_id);
        $query->whereAny('section_id', $request->section_id);

        $datas  = $query->paginate($request->pagination);
        return new Resource($datas);
    }

    /**
     * Students for marks entry data format
     *
     * @return \Illuminate\Http\Response
     */
    public function studentsForMarksEntry(Request $request)
    {
        $subjectID          = $request->subject_id;
        $resultID           = $request->result_id;
        $resultData         = null;
        $pluckStudents      = null;

        // existing students
        if (!empty($request->result_id)) {
            $resultDetails = SecondaryClassTestResultDetails::with('student')
                ->whereSub('marks', 'subject_id', $subjectID)
                ->with(['marks' => function ($q) use ($subjectID) {
                    $q->where('subject_id', $subjectID);
                }])
                ->where('secondary_class_test_result_id', $resultID)->get();

            if ($resultDetails->count() > 0) {
                $resultData =  $resultDetails->toArray();
                $pluckStudents = $resultDetails->pluck('student_id')->toArray();
            }
        }

        // get students
        $studentsQuery = Student::select(
            'id',
            'name_en',
            'software_id'
        )->with('profile')
            ->orderBy('software_id', 'asc')
            ->when($pluckStudents, function ($q)  use ($pluckStudents) {
                return $q->whereNotIn('id', $pluckStudents);
            })
            ->where([
                'institution_id'    => $request->institution_id,
                'campus_id'         => $request->campus_id,
                'shift_id'          => $request->shift_id,
                'medium_id'         => $request->medium_id,
                'group_id'          => $request->group_id,
                'section_id'        => $request->section_id,
                'academic_class_id' => $request->academic_class_id,
                'status'            => 'active'
            ]);

        $students = $studentsQuery->get()->toArray();

        $subjectAssign = SecondarySubjectAssign::where([
            'institution_id'    => $request->institution_id,
            'medium_id'         => $request->medium_id,
            'academic_class_id' => $request->academic_class_id,
        ])->first();

        if (empty($subjectAssign)) {
            return response()->json(['message' => 'Subject not assign for this class'], 422);
        }

        $subjectMarks =  $subjectAssign->details()->with('subject')->where('subject_id', $request->subject_id)->first();
        if (empty($subjectMarks)) {
            return response()->json(['message' => 'Subject not assign for this class'], 422);
        }

        $details = $this->marksFormat($students, $subjectMarks);

        if (!empty($resultData)) {
            $details = array_merge($resultData, $details);
        }

        usort($details, function ($a, $b) {
            $rollA = $a['student']['roll_number'] ?? '';
            $rollB = $b['student']['roll_number'] ?? '';

            if ($rollA === $rollB) {
                return 0;
            }
            // Handle empty "roll_number" values
            if (empty($rollA)) {
                return 1;
            }
            if (empty($rollB)) {
                return -1;
            }
            return $rollA - $rollB;
        });

        return $details;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layouts.backend_app');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->validateCheck($request)) {
            try {
                $data = $request->all();

                $result = SecondaryClassTestResult::firstOrCreate([
                    'academic_session_id' => $request->academic_session_id,
                    'institution_id' => $request->institution_id,
                    'campus_id' => $request->campus_id,
                    'shift_id' => $request->shift_id,
                    'medium_id' => $request->medium_id,
                    'academic_class_id' => $request->academic_class_id,
                    'group_id' => $request->group_id,
                    'section_id' => $request->section_id,
                    'exam_id' => $request->exam_id,
                ], $data);

                return response()->json(['message' => 'Create Successfully!', 'id' => $result->id], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Result\SecondaryClassTestResult  $secondaryClassTestResult
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SecondaryClassTestResult $secondaryClassTestResult)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }

        $search['type'] = $request->type;
        $search['field_name'] = $request->field_name;
        $search['value'] = $request->search_keyword;

        $query = SecondaryClassTestResult::with(
            'details.marks',
            'details.student',
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

        if (!empty($request->result_view)) {
            $query->with(['details' => function ($q) use ($search) {
                $q->select(
                    'secondary_class_test_result_details.*',
                    'std.software_id',
                    'std.name_en',
                    'profile.roll_number'
                )->join('students as std', 'secondary_class_test_result_details.student_id', '=', 'std.id')
                    ->join('student_profiles as profile', 'std.id', '=', 'profile.student_id');

                $q->with(['marks' => function ($mq) {
                    $mq->with('subject')->select('secondary_class_test_result_details_id', 'subject_id')->where('result_status', 'FAILED');
                }]);

                if (!empty($search['field_name']) && !empty($search['value'])) {
                    $q->whereLike($search['field_name'], $search['value']);
                }

                if ($search['type'] == 'merit') {
                    $q->where('result_status', 'PASSED')->orderBy('total_mark', 'desc');
                } else  if ($search['type'] == 'unmerit') {
                    $q->where('result_status', 'FAILED')->orderBy('total_mark', 'asc');
                } else {
                    $q->orderBy('profile.roll_number', 'asc');
                }
            }]);
            $data['result'] = $result = $query->find($secondaryClassTestResult->id);

            if (!empty($result)) {
                $session = $result->academic_session->name ?? '';
                $level = $result->qualification->name ?? '';
                $dept = $result->department->name ?? '';
                $className = $result->academic_class->name ?? '';
                $exam = $result->exam->name ?? '';
                $data['excel_header'] = [
                    "Session: {$session}",
                    "Academic Level: {$level}",
                    "Department/Group: {$dept}",
                    "Academic Class: {$className}",
                    "Exam Name: {$exam}",
                ];
            }
            return $data;
        }

        return $query->find($secondaryClassTestResult->id);
    }

    /**
     * Marksheet
     *
     * @return \Illuminate\Http\Response
     */
    public function marksheet($id)
    {
        return SecondaryClassTestResultDetails::with(
            'student',
            'marks.subject',
            'class_test_result.academic_session',
            'class_test_result.institution',
            'class_test_result.campus',
            'class_test_result.shift',
            'class_test_result.medium',
            'class_test_result.academic_class',
            'class_test_result.group',
            'class_test_result.section',
            'class_test_result.exam'
        )->find($id) ?? [];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SecondaryClassTestResult  $secondaryClassTestResult
     * @return \Illuminate\Http\Response
     */
    public function edit(SecondaryClassTestResult  $secondaryClassTestResult)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Result\SecondaryClassTestResult  $secondaryClassTestResult
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SecondaryClassTestResult  $secondaryClassTestResult)
    {
        if ($this->validateCheck($request, $secondaryClassTestResult->id)) {
            try {
                $details = $request->details;
                $marks = $details['marks'][0] ?? [];
                unset($details['marks'], $details['student']);

                if ($secondaryClassTestResult) {
                    $resultDetails = $secondaryClassTestResult->details()->updateOrCreate(
                        ['secondary_class_test_result_id' => $secondaryClassTestResult->id, 'student_id' => $details['student_id']],
                        $details
                    );

                    if ($resultDetails) {
                        $marks['subject_id'] = $request->subject_id;
                        $marks['result_status'] = $marks['mark'] < $marks['pass_mark'] ? 'FAILED' : 'PASSED';
                        $resultDetails->marks()->updateOrCreate(
                            ['secondary_class_test_result_details_id' => $resultDetails->id, 'subject_id' => $request->subject_id],
                            $marks
                        );
                    }
                }
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Result\SecondaryClassTestResult  $secondaryClassTestResult
     * @return \Illuminate\Http\Response
     */
    public function destroy(SecondaryClassTestResult  $secondaryClassTestResult)
    {
        if ($secondaryClassTestResult->status == 'deactive') {
            $secondaryClassTestResult->delete();
        } else {
            $secondaryClassTestResult->update(['status' => 'deactive']);
        }
        return response()->json(['message' => 'Delete Successfully!'], 200);
    }

    /**
     * Published result
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function published(Request $request)
    {
        $classTestResult = SecondaryClassTestResult::find($request->result_id);

        $status = $classTestResult->status == 'published' ? 'draft' : 'published';
        $msg = $classTestResult->status == 'Published' ? '' : 'Unpublished';

        $arr = [
            'status' => $status,
            'published_date' => $request->published_date
        ];

        if ($classTestResult->update($arr)) {
            return response()->json(['message' => 'Published Successfully!'], 200);
        } else {
            return response()->json(['error' => "{$msg} Unsuccessfully!"], 200);
        }
    }

    /**
     * Sync result
     * 
     * @param  \App\Models\Result\SecondaryClassTestResult  $secondaryClassTestResult
     * @return \Illuminate\Http\Response
     */
    public function syncResult(SecondaryClassTestResult $secondaryClassTestResult)
    {
        $secondaryClassTestResult->load('details.marks');
        $this->resultSync($secondaryClassTestResult);

        return response()->json(['message' => 'Result Sync Successfully!'], 200);
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck($request, $id = null)
    {
        return true;
        return $request->validate([
            //ex: 'name' => 'required|email|nullable|date|string|min:0|max:191',
        ], [
            //ex: 'name' => "This name is required" (custom message)
        ]);
    }
}
