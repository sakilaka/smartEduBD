<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Result\Secondary;

use App\Http\Controllers\Backend\Result\Secondary\Traits\ResultTrait;
use App\Http\Resources\Resource;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterSetup\Exam;
use App\Models\Student;
use App\Models\MasterSetup\Institution;
use App\Models\Result\SecondaryClassTestResult;
use App\Models\Result\SecondaryResult;
use App\Models\Result\SecondarySubjectAssign;
use Illuminate\Support\Facades\DB;

class SecondaryResultController extends Controller
{
    use ResultTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $query = SecondaryResult::latest('id')->with(
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
        $examID             = $request->exam_id;
        $resultID           = $request->result_id;
        $resultData         = null;
        $pluckStudents      = [];

        // existing students
        if (!empty($request->result_id)) {
            $resultData     = $this->subjectWiseStudent($resultID, $subjectID)['resultData'];
            $pluckStudents  = $this->subjectWiseStudent($resultID, $subjectID)['pluckStudents'];
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
                'academic_session_id' => $request->academic_session_id,
                'campus_id'         => $request->campus_id,
                'shift_id'          => $request->shift_id,
                'medium_id'         => $request->medium_id,
                'group_id'          => $request->group_id,
                'section_id'        => $request->section_id,
                'academic_class_id' => $request->academic_class_id,
                'status'            => 'active',
            ]);

        $students = $studentsQuery->get()->toArray();

        $subjectAssign = SecondarySubjectAssign::where([
            'institution_id'    => $request->institution_id,
            'medium_id'         => $request->medium_id,
            'academic_class_id' => $request->academic_class_id,
            'academic_group_id' => $request->group_id,
        ])->first();

        if (empty($subjectAssign)) {
            return response()->json(['message' => 'Subject not assign for this class'], 422);
        }

        $subjectMarks =  $subjectAssign->details()->with('subject')->where('subject_id', $request->subject_id)->first();
        if (empty($subjectMarks)) {
            return response()->json(['message' => 'Subject not assign for this class'], 422);
        }

        // class test marks
        $exam = Exam::find($examID);
        $classTestId = $exam->class_test_exam_id;

        $classTest = SecondaryClassTestResult::where([
            'academic_session_id' => $request->academic_session_id,
            'institution_id'    => $request->institution_id,
            'campus_id'         => $request->campus_id,
            'shift_id'          => $request->shift_id,
            'medium_id'         => $request->medium_id,
            'group_id'          => $request->group_id,
            'section_id'        => $request->section_id,
            'academic_class_id' => $request->academic_class_id,
            'exam_id'           => $classTestId,
            'status'            => 'published',
        ])->first();

        $details = $this->marksFormat($students, $subjectMarks, $classTest);

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

        $ctMarks = !empty($classTest) ? 1 : 0;
        return response()->json([
            'details' => $details,
            'class_test_marks_added' => $ctMarks
        ], 200);
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

                $result = SecondaryResult::where([
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

                if (empty($result)) {
                    $result = SecondaryResult::create($data);
                }

                return response()->json(['message' => 'Create Successfully!', 'id' => $result->id], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SecondaryResult  $secondaryResult
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SecondaryResult $secondaryResult)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }

        $item = SecondaryResult::with('details.marks', 'details.student')->find($secondaryResult->id);
        return $item;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SecondaryResult  $secondaryResult
     * @return \Illuminate\Http\Response
     */
    public function edit(SecondaryResult $secondaryResult)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SecondaryResult  $secondaryResult
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SecondaryResult $secondaryResult)
    {
        if ($this->validateCheck($request, $secondaryResult->id)) {
            try {
                $subject = $request->subject;
                $details = $request->details;
                $marks = $details['marks'][0] ?? [];
                unset($details['marks'], $details['student']);

                // pass marks
                $passMarks = [
                    "ct_pass" => $subject['ct_pass_mark'] ?? '',
                    "cq_pass" => $subject['cq_pass_mark'] ?? '',
                    "mcq_pass" => $subject['mcq_pass_mark'] ?? '',
                ];

                if ($secondaryResult) {

                    $resultDetails = $secondaryResult->details()->updateOrCreate(
                        ['secondary_result_id' => $secondaryResult->id, 'student_id' => $details['student_id']],
                        $details
                    );

                    if ($resultDetails) {
                        // subject assign
                        $subjectAssign = SecondarySubjectAssign::where([
                            'institution_id'    => $secondaryResult->institution_id,
                            'medium_id'         => $secondaryResult->medium_id,
                            'academic_class_id' => $secondaryResult->academic_class_id,
                        ])->first();
                        $checkFourthSubject = $subjectAssign->details()
                            ->where('subject_id', $request->subject_id)
                            ->where('fourth_subject', 1)
                            ->first();

                        $marks['subject_id'] = $request->subject_id;
                        $marks['pass_marks'] = $passMarks;
                        $marks['fourth_subject'] = !empty($checkFourthSubject) ? 1 : 0;

                        $resultDetails->marks()->updateOrCreate(
                            ['secondary_result_details_id' => $resultDetails->id, 'subject_id' => $request->subject_id],
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
     * @param  \App\Models\SecondaryResult  $secondaryResult
     * @return \Illuminate\Http\Response
     */
    public function destroy(SecondaryResult $secondaryResult)
    {
        if ($secondaryResult->status == 'deactive') {
            $secondaryResult->delete();
        } else {
            $secondaryResult->update(['status' => 'deactive']);
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
        $result = SecondaryResult::find($request->result_id);

        $status =  $result->status == 'draft' ? 'published' : 'draft';
        $arr = [
            'status' => $status,
            'published_date' => $request->published_date
        ];

        $msg = $result->status == 'draft' ? 'Published' : 'Unpublished';
        if ($result->update($arr)) {
            return response()->json(['message' => "{$msg} Successfully!"], 200);
        } else {
            return response()->json(['error' => "{$msg} Unsuccessfully!"], 200);
        }
    }

    /**
     * Sync result
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SecondaryResult $secondaryResult
     * @return \Illuminate\Http\Response
     */
    public function syncResult(Request $request, SecondaryResult $secondaryResult, $convert)
    {
        if ($secondaryResult) {
            $secondaryResult->update(['certificate_bg' => $request->certificate_bg]);
            $secondaryResult->load('details');

            if ($request->subject_id) {
                $this->studnentMarkEntry($secondaryResult, $request->subject_id);
                return response()->json(['message' => 'Student Marks Sync Successfully!'], 200);
            }

            $this->resultMarksSync($secondaryResult, $convert);
            $this->resultDetailsSync($secondaryResult);
            $this->highestMarkSync($secondaryResult);
            $this->meritPositionSync($secondaryResult);
            $this->meritPositionSync($secondaryResult, 'merit_position_in_class');
            $this->meritPositionSync($secondaryResult, 'merit_position_in_section');

            return response()->json(['message' => 'Result Sync Successfully!'], 200);
        }

        return response()->json(['message' => 'Result Sync Unsuccessfully!'], 200);
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
