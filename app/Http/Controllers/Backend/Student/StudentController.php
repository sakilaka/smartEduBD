<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Student;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Imports\StudentImport;
use App\Models\Student;
use App\Traits\SMSTrait;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Guardian;
use App\Models\StudentProfile;
use App\Models\MasterSetup\Institution;
use App\Models\Result\SecondarySubjectAssign;
use App\Models\StudentDiscount;
use App\Models\StudentSubjectAssign;
use App\Models\TeacherSubjectAssign;
use App\Traits\ImageUpload;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    use SMSTrait, ImageUpload;

    // for image upload
    protected $withDateFolder = true;
    protected $resize = [300, 300];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $query = Student::query();
        $query->whereAny('institution_id', $institution_id);
        $query->whereSub('academic_class', 'institution_category_id', $request->institution_category_id);
        $query->whereAny('academic_session_id', $request->academic_session_id);
        $query->whereAny('campus_id', $request->campus_id);
        $query->whereAny('shift_id', $request->shift_id);
        $query->whereAny('medium_id', $request->medium_id);
        $query->whereAny('academic_class_id', $request->academic_class_id);
        $query->whereAny('group_id', $request->group_id);
        $query->whereAny('section_id', $request->section_id);
        $query->whereSub('profile', 'gender', $request->gender);
        $query->whereAny('status', $request->status);

        if (in_array($request->field_name, ['roll_number'])) {
            $query->whereSubLike('profile', $request->field_name, $request->value);
        } else if (in_array($request->field_name, ['guardian_mobile'])) {
            $query->whereSubLike('guardian', 'mobile', $request->value);
        } else {
            $query->whereLike($request->field_name, $request->value);
        }

        if (!empty($request->allData)) {
            if ($request->discountStudent || $request->discount_type) {
                $query->with(['discounts' => function ($subQuery) use ($request) {
                    if (!$request->discount_type) {
                        $subQuery->whereAny('account_head_id', $request->account_head_id);
                    }

                    $subQuery->where([
                        'institution_id' => $request->institution_id,
                        'academic_session_id' => $request->academic_session_id,
                        'academic_class_id' => $request->academic_class_id
                    ]);
                }]);
            }

            if ($request->discount_type) {
                $query->whereHas('discounts', function ($subQuery) use ($request) {
                    $subQuery->whereAny('account_head_id', $request->account_head_id);
                    $subQuery->where([
                        'institution_id' => $request->institution_id,
                        'academic_session_id' => $request->academic_session_id,
                        'academic_class_id' => $request->academic_class_id
                    ]);
                    $subQuery->where('amount', '>', 0);
                });
            }

            return $query->with(
                'guardian:id,name_en,mobile',
                'profile:student_id,roll_number,profile'
            )->select('id', 'guardian_id', 'software_id', 'name_en', 'status')->orderBy(
                StudentProfile::select('roll_number')->whereColumn('student_profiles.student_id', 'students.id'),
                'asc'
            )
                ->get();
        } else {
            $query->with(
                'profile',
                'guardian',
                'academic_session',
                'campus',
                'shift',
                'medium',
                'academic_class',
                'group',
                'section',
            )
                ->oldest('academic_class_id')
                ->orderBy(
                    StudentProfile::select('roll_number')->whereColumn('student_profiles.student_id', 'students.id'),
                    'asc'
                );

            $datas = $query->paginate($request->pagination);
            return new Resource($datas);
        }
    }

    public function create()
    {
        return view('layouts.backend_app');
    }


    public function store(Request $request)
    {
        if ($this->validateCheck($request)) {
            try {
                DB::beginTransaction();

                $data       = json_decode($request->data, true);
                $profileData  = $data['profile'];
                $guardianData = $data['guardian'];
                unset($data['profile'], $data['guardian']);

                // create guardian
                $guardian = Guardian::firstOrCreate(['mobile' => $guardianData['mobile']], $guardianData);

                // create student
                $data['software_id'] = GlobalHelper::generate_id(Student::class, 'software_id', [
                    'conditions' => ['institution_id' => $data['institution_id']],
                    'pad_length' => 5,
                    'prefix'     => $data['institution_id'],
                ]);

                $data['status']     = 'active';
                $data['guardian_id'] = $guardian->id;
                $data['name_en']    = strtoupper($request->name_en);

                $student = Student::create($data);

                // create profile
                if ($request->hasFile('image')) {
                    $institutionName = Str::slug(Institution::where('id', $data['institution_id'])->value('short_name'));
                    $path = "{$institutionName}/student-profile";
                    $profileData['profile'] = $this->upload($request->file('image'), $path);
                }

                $profileData['fathers_name_en'] = strtoupper($profileData['fathers_name_en'] ?? '');
                $profileData['mothers_name_en'] = strtoupper($profileData['mothers_name_en'] ?? '');

                $student->profile()->create($profileData);

                DB::commit();
                // Send SMS
                if ($student) {
                    // $this->sendSms($student->mobile, 'StudentCreate', ['password' => $request->password ?? ''], $student);
                }
                return response()->json(['message' => 'Student create successfully!', 'id' => $student->id ?? ''], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }


    public function show(Request $request, Student $student)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        $student->load([
            'profile',
            'guardian',
            'institution',
            'academic_session',
            'shift',
            'campus',
            'medium',
            'group',
            'section',
            'academic_class',
            'promoted',
        ]);
        return $student;
    }


    public function edit(Student $student)
    {
        return view('layouts.backend_app');
    }


    public function update(Request $request, Student $student)
    {
        // dd($request->all());
        if ($this->validateCheck($request, $student->id)) {
            try {
                DB::beginTransaction();

                $image      = $request->file('image');
                $data       = json_decode($request->data, true);
                $profileData  = $data['profile'];
                $guardianData = $data['guardian'];
                unset($data['profile'], $data['guardian']);

                // update guardian
                $guardian = Guardian::find($data['guardian_id']);
                if ($guardian->mobile == $guardianData['mobile']) {
                    $guardian->update($guardianData);
                } else {
                    $guardian = Guardian::firstOrCreate(
                        ['mobile' => $guardianData['mobile']],
                        $guardianData
                    );
                }

                // update student
                $data['guardian_id']    = $guardian->id;
                $data['name_en']        = strtoupper($request->name_en);
                $student->update($data);

                // update profile
                $profile = StudentProfile::where('student_id', $student->id)->first();
                if ($request->hasFile('image')) {
                    $institutionName = Str::slug(Institution::where('id', $data['institution_id'])->value('short_name'));
                    $path = "{$institutionName}/student-profile";
                    $profileData['profile'] = $this->upload($image, $path, $profile->profile);
                } else {
                    $profileData['profile'] = $this->getOriginalPath($profile->profile);
                }

                $profileData['fathers_name_en'] = strtoupper($profileData['fathers_name_en'] ?? '');
                $profileData['mothers_name_en'] = strtoupper($profileData['mothers_name_en'] ?? '');

                $profile->update($profileData);

                DB::commit();
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }


    public function destroy(Student $student, Request $request)
    {
        if (!empty($request->bulk_id)) {
            $ids = json_decode($request->bulk_id, true);
            Student::whereIn('id', $ids)->update(['status' => 'deactive']);
            return response()->json(['message' => 'Delete Successfully!'], 200);
        }

        if ($student->update(['status' => 'deactive'])) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
        }
    }


    public function discount(Request $request)
    {
        try {
            DB::beginTransaction();

            $student   = Student::find($request->student_id);
            $commonArr = [
                'institution_id' => $student->institution_id,
                'academic_session_id' => $student->academic_session_id,
                'academic_class_id' => $student->academic_class_id,
                'account_head_id' => $request->account_head_id,
                'student_id' => $request->student_id,
            ];


            if ($request->discount == 0) {
                DB::commit();
                StudentDiscount::where($commonArr)->delete();
                return response()->json(['message' => 'Discount delete successfully!'], 200);
            }

            $data = $commonArr;
            $data['amount'] = $request->discount;

            StudentDiscount::updateOrCreate($commonArr, $data);

            DB::commit();
            return response()->json(['message' => 'Discount successfully!'], 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(['exception' => $ex->getMessage()], 422);
        }
    }


    public function import(Request $request)
    {
        try {
            $data = $request->except('excel');
            Excel::import(new StudentImport($data), $request->file('excel'));

            return response()->json(['message' => 'Import Successfully!'], 200);
        } catch (Exception $ex) {
            return response()->json(['exception' => $ex->getMessage()], 422);
        }
    }

    public function validateCheck($request, $id = null)
    {
        // return $request->validate([
        //     "academic_class_id"         => "required",
        //     "academic_session_id"       => "required",
        //     "academic_qualification_id" => "required",
        //     "student_type"              => "required",
        //     "name"                      => "required",
        //     "mobile"                    => "required|unique:students,mobile," . $id,
        //     "email"                     => "nullable|unique:students,email," . $id,
        // ]);

        return true;
    }


    public function adminAdmitCard()
    {
        return view('layouts.backend_app');
    }

    public function downloadBulkAdminAdmit(Request $request)
    {
        $searchParams = json_decode($request->search_params, true);
        // dd($searchParams);
        if (empty($searchParams)) return back();

        // Load student data with necessary relationships
        $students = Student::with([
            'institution',
            'profile',
            'academic_session',
            'academic_class'
        ])  ->where('status', 'active')
            ->when(!empty($searchParams['academic_session_id']), function ($query) use ($searchParams) {
                $query->where('academic_session_id', $searchParams['academic_session_id']);
            })
            ->when(!empty($searchParams['institution_id']), function ($query) use ($searchParams) {
                $query->where('institution_id', $searchParams['institution_id']);
            })
            ->when(!empty($searchParams['campus_id']), function ($query) use ($searchParams) {
                $query->where('campus_id', $searchParams['campus_id']);
            })
            ->when(!empty($searchParams['shift_id']), function ($query) use ($searchParams) {
                $query->where('shift_id', $searchParams['shift_id']);
            })
            ->when(!empty($searchParams['medium_id']), function ($query) use ($searchParams) {
                $query->where('medium_id', $searchParams['medium_id']);
            })
            ->when(!empty($searchParams['academic_class_id']), function ($query) use ($searchParams) {
                $query->where('academic_class_id', $searchParams['academic_class_id']);
            })
            ->when(!empty($searchParams['group_id']), function ($query) use ($searchParams) {
                $query->where('group_id', $searchParams['group_id']);
            })
            ->when(!empty($searchParams['section_id']), function ($query) use ($searchParams) {
                $query->where('section_id', $searchParams['section_id']);
            })
            ->when(!empty($searchParams['gender']), function ($query) use ($searchParams) {
                $query->whereHas('profile', function ($q) use ($searchParams) {
                    $q->where('gender', $searchParams['gender']);
                });
            })
            ->get();



        // dd($students);
        // foreach ($students as $key => $value) {
        //     dd($value->profile);
        // }
        $students = $students->sortBy(function ($student) {
            return $student->profile->roll_number ?? 0;
        })->values();


        // Generate PDF with all students
        $pdf = Pdf::loadView('pdf.admin_admit_card', compact('students', 'searchParams'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('admit_card.pdf');
    }

    public function downloadBulkSeatCard(Request $request)
    {
        $searchParams = json_decode($request->search_params, true);
        // dd($searchParams);
        if (empty($searchParams)) return back();

        // Load student data with necessary relationships
        $students = Student::with([
            'institution',
            'profile',
            'academic_session',
            'academic_class'
        ])  ->where('status', 'active')
            ->when(!empty($searchParams['academic_session_id']), function ($query) use ($searchParams) {
                $query->where('academic_session_id', $searchParams['academic_session_id']);
            })
            ->when(!empty($searchParams['institution_id']), function ($query) use ($searchParams) {
                $query->where('institution_id', $searchParams['institution_id']);
            })
            ->when(!empty($searchParams['campus_id']), function ($query) use ($searchParams) {
                $query->where('campus_id', $searchParams['campus_id']);
            })
            ->when(!empty($searchParams['shift_id']), function ($query) use ($searchParams) {
                $query->where('shift_id', $searchParams['shift_id']);
            })
            ->when(!empty($searchParams['medium_id']), function ($query) use ($searchParams) {
                $query->where('medium_id', $searchParams['medium_id']);
            })
            ->when(!empty($searchParams['academic_class_id']), function ($query) use ($searchParams) {
                $query->where('academic_class_id', $searchParams['academic_class_id']);
            })
            ->when(!empty($searchParams['group_id']), function ($query) use ($searchParams) {
                $query->where('group_id', $searchParams['group_id']);
            })
            ->when(!empty($searchParams['section_id']), function ($query) use ($searchParams) {
                $query->where('section_id', $searchParams['section_id']);
            })
            ->when(!empty($searchParams['gender']), function ($query) use ($searchParams) {
                $query->whereHas('profile', function ($q) use ($searchParams) {
                    $q->where('gender', $searchParams['gender']);
                });
            })
            ->get();



        // dd($students);
        // foreach ($students as $key => $value) {
        //     dd($value->profile);
        // }

        $students = $students->sortBy(function ($student) {
            return $student->profile->roll_number ?? 0;
        })->values();


        // Generate PDF with all students
        $pdf = Pdf::loadView('pdf.seat_card', compact('students', 'searchParams'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('seat_card.pdf');
    }




    public function studentSubjectAssignFunction($id)
    {
        $data = [];
        $student = Student::find($id);
        // dd($student);

        $data['subjectAssign'] = SecondarySubjectAssign::with([
            'details:secondary_subject_assign_id,subject_id,except_subject_id,fourth_subject,main_subject',
            'details.subject'
        ])->where([
            'institution_id'            => $student->institution_id,
            'academic_group_id'         => $student->group_id,
            'academic_class_id'         => $student->academic_class_id,
        ])->first();

        $data['assign_subjects'] = StudentSubjectAssign::with('subject')
            ->where([
                'group_id' => $student->group_id,
                'academic_class_id' => $student->academic_class_id,
                'student_id' => $student->id,
            ])->get();

        return response()->json($data);
    }

    public function classWiseSubject(Request $request)
    {
        $department_id = $request->department_id ?? auth()->guard('admin')->user()->department_id;
        $subjectID = [];
        $subjects = [];
        $subjectAssign = SecondarySubjectAssign::where([
            // 'academic_qualification_id' => $request->academic_qualification_id,
            'department_id'             => $department_id,
            'academic_class_id'         => $request->academic_class_id,
        ])->first();

        if (auth()->user()->type == 'Teacher') {
            $subjectID = TeacherSubjectAssign::getAssignId('subject_id', $request->all());
        }

        if (!empty($subjectAssign)) {
            if (auth()->user()->type == 'Teacher') {
                $subjects = $subjectAssign->details()->whereIn('subject_id', $subjectID)->with('subject')->get();
            } else {
                $subjects = $subjectAssign->details()->with('subject')->get();
            }
        }

        return response()->json($subjects);
    }

    public function storeSubjectAssign(Request $request)
    {
        try {
            $student = Student::find($request->student_id);

            // delete existing subjects
            StudentSubjectAssign::where([
                'group_id' => $student->group_id,
                'academic_class_id' => $student->academic_class_id,
                'student_id' => $student->id,
            ])->delete();

            // store subjects
            $inputs = $request->data;
            foreach ($inputs as $key => $subject) {
                $data = [
                    'group_id' => $student->group_id,
                    'academic_class_id' => $student->academic_class_id,
                    'student_id' => $student->id,
                    'subject_id' => $subject['subject_id'],
                    'main_subject' => $subject['main_subject']
                ];
                StudentSubjectAssign::create($data);
            }
            return response()->json(['message' => "Subject Assign Successfully"], 200);
        } catch (\Exception $ex) {
            return response()->json(['exception' => $ex->errorInfo ?? $ex->getMessage()], 422);
        }
    }
}
