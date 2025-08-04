<?php

namespace App\Http\Controllers\Backend\Result\HigherSecondary;

use App\Http\Controllers\Backend\Result\HigherSecondary\Traits\ResultTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\MasterSetup\Exam;
use App\Models\MasterSetup\Subject;
use App\Models\Result\HigherSecondaryResult;
use App\Models\Result\HigherSecondaryResultDetails;
use App\Models\Result\HigherSecondarySubjectAssign;
use App\Models\Result\HigherSecondarySubjectAssignDetails;
use App\Models\Result\Result;
use App\Models\Result\ResultDetails;
use App\Models\Result\SubjectAssign;
use App\Models\Student;
use App\Models\StudentSubjectAssign;
use Exception;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    use ResultTrait;


    public function index(Request $request)
    {
        // dd('enter');
        try {
            $query  = HigherSecondaryResult::latest('id')->with(
                'academic_class',
                'academic_session',
                'academic_group',
                'exam',
            );

            // if (auth()->user()->type == 'Teacher') {
            //     $authID = auth()->id();
            //     $query->whereIn('id', function ($subQuery) use ($authID) {
            //         $subQuery->select('r.id')
            //             ->from('results as r')
            //             ->join('teacher_subject_assigns as a', function ($join) use ($authID) {
            //                 $join->on('r.department_id', '=', 'a.department_id')
            //                     ->whereColumn('r.academic_qualification_id', '=', 'a.academic_qualification_id')
            //                     ->whereColumn('r.academic_class_id', '=', 'a.academic_class_id')
            //                     ->where('a.admin_id', '=', $authID)
            //                     ->where('a.status', '=', 'active');
            //             })
            //             ->groupBy('r.id');
            //     });
            // }

            $query->whereAny('academic_session_id', $request->academic_session_id);
            $query->whereAny('academic_class_id', $request->academic_class_id);
            $query->whereAny('academic_group_id', $request->academic_group_id);
            // $query->whereAny('department_id', $request->department_id);

            $datas  = $query->paginate($request->pagination);
            return new Resource($datas);
        } catch (\Exception $th) {
            return $th->getMessage();
        }

    }


    public function studentsForMarksEntry(Request $request)
    {
        // dd($request->all());
        $subjectID          = $request->subject_id;
        $examID             = $request->exam_id;
        $resultID           = $request->result_id;
        $selectedStudent    = $request->selected_student ?? false;

        $resultData         = null;
        $pluckStudents      = null;
        $pluckSubStudnets   = null;

        // existing students
        if (!empty($request->result_id)) {
            if ($request->total_exam_subjects) {
                HigherSecondaryResult::find($request->result_id)->update(['total_exam_subjects' => $request->total_exam_subjects]);
            }
            $resultDetails = HigherSecondaryResultDetails::with('student')
                ->whereSub('marks', 'subject_id', $subjectID)
                ->with(['marks' => function ($q) use ($subjectID) {
                    $q->where('subject_id', $subjectID);
                }])
                ->where('result_id', $resultID)->get();

            if ($resultDetails->count() > 0) {
                $resultData =  $resultDetails->toArray();
                $pluckStudents = $resultDetails->pluck('student_id')->toArray();
            }
        }

        // get students
        $studentsQuery = Student::select(
            'id',
            'student_id',
            'name',
            'college_roll'
        )
            ->orderBy('college_roll', 'asc')
            ->when($pluckStudents, function ($q)  use ($pluckStudents) {
                return $q->whereNotIn('id', $pluckStudents);
            })
            ->where([
                'academic_session_id'       => $request->academic_session_id,
                'department_id'             => $request->department_id,
                'academic_qualification_id' => $request->academic_qualification_id,
                'academic_class_id'         => $request->academic_class_id,
                'status'                    => 'active',
            ]);



        // subject selected students
        if (!empty((int) $selectedStudent)) {
            $parentSubId = Subject::where('id', $subjectID)->value('parent_id');
            $pluckSubStudnets = StudentSubjectAssign::where([
                'department_id'     => $request->department_id,
                'academic_class_id' => $request->academic_class_id,
                'subject_id'        => $parentSubId ?? $subjectID,
            ])->pluck('student_id')->toArray();

            $studentsQuery->whereIn('id', $pluckSubStudnets);
        }

        $students = $studentsQuery->get()->toArray();
        // dd($students);

        $subjectAssign = HigherSecondarySubjectAssign::where([
            'department_id'             => $request->department_id,
            'academic_qualification_id' => $request->academic_qualification_id,
            'academic_class_id'         => $request->academic_class_id,
        ])->first();


        if (empty($subjectAssign)) {
            return response()->json(['message' => 'Subject not assign for this class'], 422);
        }


        $allChildSubject = Subject::where('is_child', 1)->get();
        $matchedChild = $allChildSubject->firstWhere('id', $request->subject_id);
        $lookupSubjectId = $matchedChild ? $matchedChild->parent_id : $request->subject_id;

        // Now fetch subject assign details using the resolved subject ID
        $subjectMarks = $subjectAssign->details()
            ->with('subject')
            ->where('subject_id', $lookupSubjectId)
            ->first();

        // dd($subjectMarks);
        if (empty($subjectMarks)) {
            return response()->json(['message' => 'Subject not assign for this class'], 422);
        }

        // class test marks
        $exam = Exam::find($examID);
        $classTestId = $exam->class_test_exam_id;

        $classTest = "";
        // ClassTestResult::where([
        //     'academic_session_id'       => $request->academic_session_id,
        //     'department_id'             => $request->department_id,
        //     'academic_qualification_id' => $request->academic_qualification_id,
        //     'academic_class_id'         => $request->academic_class_id,
        //     'exam_id'                   => $classTestId,
        //     'status'                    => 'published',
        // ])->first();

        $details = $this->marksFormat($students, $subjectMarks, $classTest);
        // dd($details);

        if (!empty($resultData)) {
            $details = array_merge($resultData, $details);
        }

        usort($details, function ($a, $b) {
            $rollA = $a['student']['college_roll'] ?? '';
            $rollB = $b['student']['college_roll'] ?? '';

            if ($rollA === $rollB) {
                return 0;
            }
            // Handle empty "college_roll" values
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

    public function create()
    {
        return view('layouts.backend_app');
    }

    public function store(Request $request)
    {
        if ($this->validateCheck($request)) {
            try {
                $data = $request->all();

                $result = HigherSecondaryResult::where([
                    'academic_session_id' => $request->academic_session_id,
                    'academic_class_id' => $request->academic_class_id,
                    'exam_id' => $request->exam_id,
                ])->first();

                if (empty($result)) {
                    $result = HigherSecondaryResult::create($data);
                }

                return response()->json(['message' => 'Create Successfully!', 'id' => $result->id], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->errorInfo ?? $ex->getMessage()], 422);
            }
        }
    }

    public function show(Request $request, HigherSecondaryResult $result)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        // return  $result;

        $item = HigherSecondaryResult::with('details.marks', 'details.student')->find($result->id);
        // dd($item);
        return $item;
    }


    public function edit(HigherSecondaryResult $result)
    {
        return view('layouts.backend_app');
    }


    public function update(Request $request, HigherSecondaryResult $result)
    {
        if ($this->validateCheck($request, $result->id)) {
            try {
                $subject = $request->subject;
                $details = $request->details;

                if ($request->type == 'single') {
                    $this->singleResultUpdate($result, $details, $subject);
                } else if ($request->type == 'all') {
                    $this->resultUpdate($result, $details, $subject);
                }

                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->errorInfo ?? $ex->getMessage()], 422);
            }
        }
    }

    private function resultUpdate($result, $details, $subject)
    {

        $passMarks = [
            "ct_pass" => $subject['ct_pass_mark'],
            "cq_pass" => $subject['cq_pass_mark'],
            "mcq_pass" => $subject['mcq_pass_mark'],
            "practical_pass" => $subject['practical_pass_mark']
        ];

        foreach ($details as $detail) {
            $resultDetails = $result->details()->updateOrCreate(
                ['result_id' => $result->id, 'student_id' => $detail['student_id']],
                [
                    'total_mark' => $detail['total_mark'] ?? 0,
                    'gpa' => $detail['gpa'] ?? 0,
                    'letter_grade' => $detail['letter_grade'] ?? 'F',
                ]
            );

            // Then handle each mark (similar to singleResultUpdate)
            foreach ($detail['marks'] as $mark) {
                // Check if this is an additional subject
                $checkSubOptional = StudentSubjectAssign::where([
                    'department_id' => $result->department_id,
                    'academic_class_id' => $result->academic_class_id,
                    'student_id' => $detail['student_id'],
                    'subject_id' => $mark['subject_id'],
                    'main_subject' => 0
                ])->first();
                $additional_subject = !empty($checkSubOptional) ? 1 : 0;

                $markData = [
                    'subject_id' => $mark['subject_id'],
                    'ct_mark' => $mark['ct_mark'] ?? null,
                    'cq_mark' => $mark['cq_mark'] ?? null,
                    'mcq_mark' => $mark['mcq_mark'] ?? null,
                    'practical_mark' => $mark['practical_mark'] ?? null,
                    'obtained_mark' => $mark['obtained_mark'] ?? null,
                    'total_mark' => $mark['total_mark'] ?? null,
                    'gpa' => $mark['gpa'] ?? null,
                    'letter_grade' => $mark['letter_grade'] ?? null,
                    'is_absent' => $mark['is_absent'] ?? 0,
                    'pass_marks' => json_encode($passMarks),
                    'additional_subject' => $additional_subject,
                ];

                $resultDetails->marks()->updateOrCreate(
                    ['result_details_id' => $resultDetails->id, 'subject_id' => $mark['subject_id']],
                    $markData
                );
            }
        }

        return true;
    }


    private function singleResultUpdate($result, $details, $subject)
    {
        $marks = $details['marks'][0] ?? [];
        unset($details['marks'], $details['student']);

        // Step 1: If it's a child subject, fetch parent subject assign info
        if (!empty($subject['parent_id'])) {
            $subjectAssignId = HigherSecondarySubjectAssign::where('academic_qualification_id', $result->academic_qualification_id)
                ->where('department_id', $result->department_id)
                ->where('academic_class_id', $result->academic_class_id)
                ->value('id');

            $info = HigherSecondarySubjectAssignDetails::where('subject_assign_id', $subjectAssignId)
                ->where('subject_id', $subject['parent_id'])
                ->first();

            if ($info) {
                $infoArray = $info->toArray();
                $infoArray['subject_id'] = $subject['id'];

                $subject = array_merge($subject, $infoArray);
            }
        }

        // Step 2: Build pass marks
        $passMarks = [
            "ct_pass" => $subject['ct_pass_mark'],
            "cq_pass" => $subject['cq_pass_mark'],
            "mcq_pass" => $subject['mcq_pass_mark'],
            "practical_pass" => $subject['practical_pass_mark']
        ];

        // Step 3: Create/update result
        if ($result) {
            $resultDetails = $result->details()->updateOrCreate(
                ['result_id' => $result->id, 'student_id' => $details['student_id']],
                $details
            );

            if ($resultDetails) {
                $checkSubOptional = StudentSubjectAssign::where([
                    'department_id' => $result->department_id,
                    'academic_class_id' => $result->academic_class_id,
                    'student_id' => $details['student_id'],
                    'subject_id' => $subject['subject_id'],
                    'main_subject' => 0
                ])->first();

                $additional_subject = !empty($checkSubOptional) ? 1 : 0;

                $marks['subject_id'] = $subject['subject_id'];
                $marks['pass_marks'] = $passMarks;
                $marks['additional_subject'] = $additional_subject;

                $resultDetails->marks()->updateOrCreate(
                    ['result_details_id' => $resultDetails->id, 'subject_id' => $subject['subject_id']],
                    $marks
                );
            }
        }

        return true;
    }

    public function destroy(HigherSecondaryResult $result)
    {
        if ($result->status == 'deactive') {
            $result->delete();
        } else {
            $result->update(['status' => 'deactive']);
        }
        return response()->json(['message' => 'Delete Successfully!'], 200);
    }

    public function published(Request $request)
    {
        $result = HigherSecondaryResult::find($request->result_id);

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


    public function syncResult(Request $request, HigherSecondaryResult $result, $convert)
    {
        // dd($result);
        if ($result) {
            if ($request->subject_id) {
                $this->studnentMarkEntry($result, $request->subject_id);
                return response()->json(['message' => 'Student Marks Sync Successfully!'], 200);
            }

            $this->resultMarksSync($result, $convert);
            $this->resultGradeSync($result);
            $this->meritPositionSync($result, 'merit_position_in_department');
            $this->meritPositionSync($result, 'merit_position_in_class');
            return response()->json(['message' => 'Result Sync Successfully!'], 200);
        }

        return response()->json(['message' => 'Result Sync Unsuccessfully!'], 200);
    }


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
