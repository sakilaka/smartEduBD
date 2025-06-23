<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Teacher;

use App\Http\Resources\Resource;
use App\Models\TeacherAttendance;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Helpers\GlobalHelper;
use App\Models\TeacherAttendanceDetails;

class TeacherAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query  = TeacherAttendance::latest('date');
        $query->whereDates('date', $request->from_date, $request->to_date);

        $datas  = $query->paginate($request->pagination);
        return new Resource($datas);
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
                DB::beginTransaction();

                // exists check
                $exists = TeacherAttendance::where('date', $request->date)->first();
                if (!empty($exists)) {
                    return response()->json(['error' => 'Sorry!! Already Submitted'], 200);
                }

                $details = $request->details;
                $data    = $request->except('details');

                $attendance = TeacherAttendance::create($data);
                if ($attendance) {
                    $attendance->details()->createMany($details);
                }

                DB::commit();
                return response()->json(['message' => 'Create Successfully!', 'id' => $attendance->id ?? ''], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->errorInfo ?? $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TeacherAttendance  $teacherAttendance
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, TeacherAttendance $teacherAttendance)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }

        return TeacherAttendance::with('details.teacher.department')->find($teacherAttendance->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TeacherAttendance  $teacherAttendance
     * @return \Illuminate\Http\Response
     */
    public function edit(TeacherAttendance $teacherAttendance)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TeacherAttendance  $teacherAttendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeacherAttendance $teacherAttendance)
    {
        if ($this->validateCheck($request, $teacherAttendance->id)) {
            try {
                DB::beginTransaction();
                $data = $request->except('details');

                $teacherAttendance->update($data);

                // exists check
                $count = TeacherAttendance::where(['date' => $data['date']])->count();
                if ($count == 2) {
                    DB::rollBack();
                    return response()->json(['error' => 'Sorry!! Already Submitted'], 200);
                }

                // update, delete, insert
                GlobalHelper::updelsert(
                    'teacher_attendance_id',
                    $teacherAttendance->id,
                    TeacherAttendanceDetails::class,
                    $request->details
                );

                DB::commit();
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->errorInfo ?? $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TeacherAttendance  $teacherAttendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeacherAttendance $teacherAttendance)
    {
        if ($teacherAttendance->delete()) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
        }
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
