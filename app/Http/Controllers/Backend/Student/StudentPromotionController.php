<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Student;

use App\Http\Controllers\Controller;
use App\Models\Result\PrimarySubjectAssign;
use App\Models\Student;
use App\Models\StudentProfile;
use App\Models\StudentPromotion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPromotionController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('layouts.backend_app');
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
            DB::beginTransaction();
            try {
                // if (!empty($request->irregular_ids)) {
                //     Student::whereIn('id', $request->irregular_ids)->update(['student_type' => "Irregular"]);
                // }

                $promoted_rolls = $request->promoted_rolls;
                $promoted_students = Student::with('profile')->whereIn('id', $request->promoted_ids)->get();

                // Prepare data for bulk update and upsert
                $promotedHistories = [];
                $updatedStudentsData = [];
                $updatedStudentProfilesData = [];

                $promotedArr = [
                    'institution_id'        => $request->institution_id ?? '',
                    'academic_session_id'   => $request->academic_session_id ?? '',
                    'campus_id'             => $request->campus_id ?? '',
                    'shift_id'              => $request->shift_id ?? '',
                    'medium_id'             => $request->medium_id ?? '',
                    'academic_class_id'     => $request->academic_class_id ?? '',
                    'group_id'              => $request->group_id ?? '',
                    'section_id'            => $request->section_id ?? '',
                ];

                foreach ($promoted_students ?? [] as $key => $student) {
                    if ($student) {
                        $oldStudent = $student->toArray();

                        // Prepare student data for bulk update
                        $updatedStudentsData[] = array_merge($promotedArr, ['id' => $student['id']]);

                        // Prepare student profile data for bulk update
                        $updatedStudentProfilesData[] = [
                            'student_id'    => $student['id'],
                            'roll_number'   => $promoted_rolls[$student['id']],
                        ];

                        // Prepare new student data for bulk update
                        $newStudent = array_merge(
                            $oldStudent,
                            $promotedArr,
                            ['new_roll_number' => $promoted_rolls[$student['id']]]
                        );

                        // Prepare the promoted history for upsert
                        $promotedHistories[] = [
                            'student_id' => $student['id'],
                            'old_json'   => json_encode($oldStudent),
                            'new_json'   => json_encode($newStudent),
                        ];
                    }
                }

                // Bulk update student records
                if (!empty($updatedStudentsData)) {
                    Student::upsert(
                        $updatedStudentsData,
                        ['id'],
                        [
                            'institution_id',
                            'academic_session_id',
                            'campus_id',
                            'shift_id',
                            'medium_id',
                            'academic_class_id',
                            'group_id',
                            'section_id'
                        ]
                    );
                }

                // Bulk insert or update promotion histories
                if (!empty($updatedStudentProfilesData)) {
                    StudentProfile::upsert(
                        $updatedStudentProfilesData,
                        ['student_id'],
                        ['roll_number']
                    );
                }

                // Bulk insert or update promotion histories
                if (!empty($promotedHistories)) {
                    StudentPromotion::insert($promotedHistories);
                }

                DB::commit();
                return response()->json(['message' => 'Promoted Successfully!'], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }


    // Student subject assign for Higher Secondary
    // $this->subjectAssign($oldStudent, $request->academic_class_id);

    // subject assign
    function subjectAssign($student, $class_id)
    {
        // $subjectAssign = StudentSubjectAssign::select(
        //     'department_id',
        //     'academic_class_id',
        //     'student_id',
        //     'subject_id',
        //     'main_subject'
        // )->where([
        //     'student_id' => $student['id'],
        //     'department_id' => $student['department_id'],
        //     'academic_class_id' => $student['academic_class_id'],
        // ])->get()->toArray();

        // foreach ($subjectAssign ?? [] as $key => $newSub) {
        //     $newSub['academic_class_id'] = $class_id;
        //     $exists = StudentSubjectAssign::where($newSub)->exists();
        //     if ($exists) continue;
        //     StudentSubjectAssign::create($newSub);
        // }
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
