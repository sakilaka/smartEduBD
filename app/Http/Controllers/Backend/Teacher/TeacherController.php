<?php

namespace App\Http\Controllers\Backend\Teacher;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Imports\TeachersImport;
use App\Models\Admin;
use App\Models\Result\PrimarySubjectAssignDetails;
use App\Models\System\Role;
use App\Models\Teacher;
use App\Models\TeacherSubjectAssign;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::with('institution')->latest('id');

        $user = auth()->user();

        if ($user->role_id == 1) {
            $query->where('type', 'Teacher');
        } else {
            $query->where('type', 'Teacher')->where('institution_id', $user->institution_id);
        }

        if ($request->has('allData') && $request->allData) {
            return response()->json($query->get());
        } else {
            $pagination = $request->pagination ?? 10; // default to 10 per page
            return response()->json($query->paginate($pagination));
        }
    }



    public function create()
    {
        return view('layouts.backend_app');
    }


    public function store(Request $request)
    {

        try {
            // First validate the request
            if (!$this->validateCheck($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $this->validationErrors ?? 'Unknown validation errors'
                ], 422);
            }

            DB::beginTransaction();

            $data = $request->only('type', 'name', 'father_name', 'email', 'mobile', 'password', 'institution_id');
            // dd($data);

            // Check role
            $role = Role::where('name', 'Teacher')->first();

            if (!is_object($role)) {
                Log::error('Teacher role not found');
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher role not found in system'
                ], 400);
            }

            // Create admin
            $data['role_id'] = $role->id;
            Log::info('Creating admin with data:', $data);
            $admin = Admin::create($data);
            Log::info('Admin created:', ['admin_id' => $admin->id]);

            // Create teacher
            $teacherInput = $request->teacher;

            $admin->teacher()->create([
                'designation_id' => $teacherInput['designation_id'],
                'index_number' => $teacherInput['index_number'],
                'date_of_birth' => $teacherInput['date_of_birth'],
                'joining_date_lecturer' => $teacherInput['joining_date_lecturer'],
                'present_address' => $teacherInput['present_address'],
                'permanent_address' => $teacherInput['permanent_address'],
            ]);

            // dd($teacherInput);

            // Subject assign
            if (!empty($request->subject_assigns[0]) && !empty($request->subject_assigns[0]['subject_id'])) {
                GlobalHelper::updelsert(
                    'admin_id',
                    $admin->id,
                    TeacherSubjectAssign::class,
                    $request->subject_assigns
                );
            }

            // Send SMS
            // if (!empty($admin->mobile)) {
            //     Log::info('Sending SMS to:', ['mobile' => $admin->mobile]);
            //     $password = $request->password;
            //     $this->sendSms($admin->mobile, 'TeacherCreate', ['password' => $password], $admin);
            // }

            DB::commit();
            Log::info('Transaction committed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Teacher created successfully',
                'data' => [
                    'admin_id' => $admin->id,
                    'teacher_id' => $admin->teacher->id
                ]
            ], 200);
        } catch (\Illuminate\Database\QueryException $qe) {
            DB::rollBack();
            Log::error('Database Exception:', [
                'message' => $qe->getMessage(),
                'sql' => $qe->getSql(),
                'bindings' => $qe->getBindings(),
                'trace' => $qe->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => $qe->getMessage(),
                'error_info' => $qe->errorInfo ?? null
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('General Exception:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating teacher',
                'error' => $e->getMessage(),
                'exception_type' => get_class($e)
            ], 500);
        }
    }


    public function show(Request $request, $id)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }

        // Load admin with relations
        $adminModel = Admin::with('role', 'teacher.designation', 'subject_assigns.subject')->find($id);

        $admin = $adminModel ? $adminModel->toArray() : [];

        // You may be overriding designation_id here!
        $admin['teacher'] = !empty($admin['teacher']) ? $admin['teacher'] : ['designation_id' => null];

        // Process subject assigns
        if (!empty($admin['subject_assigns'])) {
            foreach ($admin['subject_assigns'] as &$assign) {
                if (!empty($assign['subject'])) {
                    $assign['filteredSubjects'] = [$assign['subject']];
                } else {
                    $assign['filteredSubjects'] = [];
                }
            }
        }

        return $admin;
    }


    public function edit($id)
    {
        return view('layouts.backend_app');
    }


    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);
        if ($this->validateCheck($request, $id)) {
            try {
                // check role create or not
                $role = Role::where('name', $request->type)->first();
                if (!is_object($role)) {
                    return response()->json(['error' => "Please create {$request->type} role"], 200);
                }

                $data = $request->except('subject_assigns', 'profile');
                $data['role_id'] = $role->id;
                $admin->update($data);

                // teacher create or update
                $teacherInput = $request->teacher;
                unset($teacherInput['signature']);
                $admin->teacher()->updateOrCreate(['admin_id' => $admin->id],  $teacherInput);

                if (!empty($request->subject_assigns[0] && !empty($request->subject_assigns[0]['subject_id']))) {
                    // update, delete, insert
                    GlobalHelper::updelsert(
                        'admin_id',
                        $admin->id,
                        TeacherSubjectAssign::class,
                        $request->subject_assigns
                    );
                }

                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->errorInfo ?? $ex->getMessage()], 422);
            }
        }
    }


    public function destroy($id)
    {
        $admin = Admin::find($id);
        if ($admin->update(['status' => 'deactive'])) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
        }
    }

    public function import(Request $request)
    {
        try {

            // check role create or not
            $role = Role::where('type', 'Teacher')->first();
            if (!is_object($role)) {
                return response()->json(['error' => "Please create Teacher role"], 200);
            }
            // check role create or not
            $role = Role::where('type', 'Staff')->first();
            if (!is_object($role)) {
                return response()->json(['error' => "Please create Staff role"], 200);
            }



            Excel::import(new TeachersImport(), $request->file('excel_file'));

            return response()->json(['message' => 'Import Successfully!'], 200);
        } catch (Exception $ex) {
            return response()->json(['exception' => $ex->errorInfo ?? $ex->getMessage()], 422);
        }
    }


    public function validateCheck($request, $id = null)
    {
        return $request->validate([
            'name'     => 'required',
            // 'email'    => 'min:2|unique:admins,email,' . $id,
            'password' => 'min:6',
            // 'type'     => 'required',
            // 'subject_assigns.*.department_id' => 'required_if:type,Teacher',
            // 'subject_assigns.*.academic_qualification_id' => 'required_if:type,Teacher',
            // 'subject_assigns.*.academic_class_id' => 'required_if:type,Teacher',
            // 'subject_assigns.*.subject_id' => 'required_if:type,Teacher',
        ]);
    }
}
