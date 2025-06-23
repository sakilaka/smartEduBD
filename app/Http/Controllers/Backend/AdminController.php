<?php
/*
 *  @Developed By: Oshit Sutra Dar
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Invoice;
use App\Models\MasterSetup\Institution;
use App\Models\Student;
use App\Traits\ImageUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use ImageUpload;
    protected $withDateFolder = false;

    public function dashboard(Request $request)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
    }

    /**
     * Get dashboard info
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboardInfo()
    {
        $year          = date('Y');
        $date          = date('Y-m-d');
        $prevDate      = date('Y-m-d', strtotime(' -1 day'));
        $prev7Date     = date('Y-m-d', strtotime(' -7 day'));
        $institution_id = Institution::current();

        // Invoice Payment & Count
        $todayInvQuery = Invoice::whereDate('payment_date', $date)->success();
        $todayInvQuery->whereAny('institution_id', $institution_id);
        $prevInvQuery = Invoice::whereDate('payment_date', $prevDate)->success();
        $prevInvQuery->whereAny('institution_id', $institution_id);
        $prev7InvQuery = Invoice::whereDate('payment_date', '>=', $prev7Date)->whereDate('payment_date', '<=', $date)->success();
        $prev7InvQuery->whereAny('institution_id', $institution_id);
        $yearInvQuery = Invoice::whereYear('payment_date', $year)->success();
        $yearInvQuery->whereAny('institution_id', $institution_id);
        $totalInvQuery = Invoice::success()->whereAny('institution_id', $institution_id);

        // Total Student
        $stdQuery = Student::where('status', 'active');
        $stdQuery->whereAny('institution_id', $institution_id);

        $data['todays_trans']         = $todayInvQuery->count();
        $data['prev_day_trans']       = $prevInvQuery->count();
        $data['todays_payment']       = $todayInvQuery->sum('amount');
        $data['prev_day_payment']     = $prevInvQuery->sum('amount');
        $data['prev7_day_payment']    = $prev7InvQuery->sum('amount');
        $data['current_year_payment'] = $yearInvQuery->sum('amount');
        $data['total_payment'] = $totalInvQuery->sum('amount');

        $data['total_student'] = $stdQuery->count();

        return response()->json($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Admin::with('role', 'institution')->where('role_id', '!=', 1)->latest('id');
        $query->whereLike($request->field_name, $request->value);
        $query->whereAny('status', $request->status);

        if ($request->allData) {
            return $query->get();
        } else {
            $datas = $query->paginate($request->pagination);
            return new Resource($datas);
        }
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
    public function profile()
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
            $res = Admin::create($request->all());
            return response()->json(['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Admin $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Admin $admin)
    {
        if ($request->route_name == 'admin.profile') {
            return Admin::with('role')->find(auth()->guard('admin')->id());
        }
        return Admin::with('role')->where('role_id', '!=', 1)->find($admin->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Admin $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Admin $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        if ($this->validateCheck($request, $admin->id)) {

            if (!empty($request->hasFile('profile'))) {
                $uploadPath = $this->upload($request->file('profile'), 'admin-profile', $admin->profile);
            }

            $arr = [
                'name'    => $request->name,
                'email'   => $request->email,
                'institution_id' => $request->institution_id ?? $admin->institution_id,
                'role_id' => $request->role_id ?? $admin->role_id,
                'mobile'  => $request->mobile,
                'profile' => $uploadPath ?? $this->getOriginalPath($admin->profile),
                'status'  => $request->status ?? $admin->status,
            ];

            if (!empty($request->password)) {
                $arr['password'] = $request->password;
            }

            $admin->update($arr);
            return response()->json(['message' => 'Information Update Successfully!'], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        if ($admin->update(['status' => 'deactive'])) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['message' => 'Delete Unsuccessfully!'], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Admin $admin
     * @return \Illuminate\Http\Response
     */
    public function checkOldPassword(Request $request)
    {
        if (Auth::guard('admin')->user()->role_id == 1 && Auth::guard('admin')->user()->id != $request->id) {
            return response()->json(true);
        }
        if (Auth::guard('admin')->validate(['password' => $request->old_password, 'id' => $request->id])) {
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }
    //password change==============
    public function passwordChange(Request $request)
    {
        $request->validate([
            'new_password'     => 'required|min:6|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required|min:6',
        ]);
        $password = Hash::make($request->new_password);
        Admin::where('id', $request->id)->update(['password' => $password]);
        return response()->json(['message' => 'Password change successfully!!'], 200);
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck($request, $id = null)
    {
        return $request->validate([
            'email'    => 'required|min:2|unique:admins,email,' . $id,
        ]);
    }
}
