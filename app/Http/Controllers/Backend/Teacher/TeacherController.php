<?php

namespace App\Http\Controllers\Backend\Teacher;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Imports\TeachersImport;
use App\Models\Admin;
use App\Models\System\Role;
use App\Models\TeacherSubjectAssign;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $deptID        = auth()->guard('admin')->user()->department_id;
        $department_id = !empty($request->department_id) ? $request->department_id : $deptID;

        $query  = Admin::with('department')->latest('id');
        $query->whereLike($request->field_name, $request->value);
        $query->whereAny('department_id', $department_id);
        $query->where('type', 'Teacher');

        if ($request->allData) {
            return $query->get();
        } else {
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
    // Start by logging the incoming request
    Log::info('Teacher Store Request:', [
        'full_url' => $request->fullUrl(),
        'method' => $request->method(),
        'input_data' => $request->all(),
        'headers' => $request->headers->all()
    ]);

    try {
        // First validate the request
        if (!$this->validateCheck($request)) {
            Log::warning('Validation failed in validateCheck');
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validationErrors ?? 'Unknown validation errors'
            ], 422);
        }

        DB::beginTransaction();
        Log::info('Transaction started');

        $data = $request->only('type', 'name', 'father_name', 'mother_name', 'email', 'mobile', 'password');
        Log::debug('Extracted data:', $data);

        // Check role
        $role = Role::where('name', 'Teacher')->first();
        Log::debug('Role fetched:', ['role' => $role]);

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
        Log::info('Creating teacher with designation:', ['designation_id' => $teacherInput['designation_id']]);
        $admin->teacher()->create(['designation_id' => $teacherInput['designation_id']]);

        // Subject assign
        if (!empty($request->subject_assigns[0]) && !empty($request->subject_assigns[0]['subject_id'])) {
            Log::info('Processing subject assignments:', $request->subject_assigns);
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
        $admin = Admin::with('role', 'teacher.designation', 'department', 'subject_assigns')
            ->where('type', '!=', 'Admin')
            ->find($id)
            ->toArray();
        $admin['teacher'] = !empty($admin['teacher']) ? $admin['teacher'] : ['designation_id' => null];
        $admin['subject_assigns'] = !empty($admin['subject_assigns']) ? $admin['subject_assigns'] : [['status' => 'active']];
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
                $role = Role::where('type', $request->type)->first();
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