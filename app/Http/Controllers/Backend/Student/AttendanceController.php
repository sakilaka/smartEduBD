<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Student;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\Attendance\Attendance;
use App\Models\Attendance\AttendanceDetails;
use App\Models\MasterSetup\Institution;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Traits\SMSTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    use SMSTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $query = Attendance::with(
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
        )
            ->withCount('present_student')
            ->latest('date');

        $query->whereAny('institution_id', $institution_id);
        $query->whereAny('campus_id', $request->campus_id);
        $query->whereAny('shift_id', $request->shift_id);
        $query->whereAny('medium_id', $request->medium_id);
        $query->whereAny('academic_class_id', $request->academic_class_id);
        $query->whereAny('group_id', $request->group_id);
        $query->whereAny('section_id', $request->section_id);
        $query->whereDates('date', $request->from_date, $request->to_date);

        $datas = $query->paginate($request->pagination);
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

                $details = $request->details;
                $data    = $request->except('details');

                // exists check
                $exists = Attendance::where($data)->first();
                if (!empty($exists)) {
                    return response()->json(['error' => 'Sorry!! Already Submitted'], 200);
                }

                $attendance = Attendance::create($data);
                if ($attendance) {
                    $detailsStore = $attendance->details()->createMany($details);

                    // absent message
                    // if ($detailsStore) {
                    //     $absentStudents = $attendance->details()->where('status', 'A')->pluck('student_id');
                    //     if (!empty($absentStudents)) {

                    //         $mobilesNumbers = Student::whereNotNull('guardian_mobile')
                    //             ->whereIn('student_id', $absentStudents)
                    //             ->where([
                    //                 'academic_session_id' => $data['academic_session_id'],
                    //                 'academic_qualification_id' => $data['academic_qualification_id'],
                    //                 'department_id' => $data['department_id'],
                    //                 'academic_class_id' => $data['academic_class_id'],
                    //                 'status' => 'active'
                    //             ])
                    //             ->pluck('guardian_mobile')->toArray();

                    //         $this->sendAbsentSms($data['date'], $mobilesNumbers);
                    //     }
                    // }
                }
                DB::commit();
                return response()->json(['message' => 'Attendance Entry Successfully!', 'id' => $attendance->id ?? ''], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    // send absent sms
    public function sendAbsentSms($date, $contacts)
    {
        if (!empty($contacts)) {
            $contacts = implode(",", $contacts);
            $date = date('D, d F Y', strtotime($date));

            $this->sendSms($contacts, 'Absent', ['date' => $date]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Attendance $attendance)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        $attendance = Attendance::with(
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
            'details.student'
        )->withCount('present_student')->find($attendance->id);

        return $attendance;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        if ($this->validateCheck($request, $attendance->id)) {
            try {
                DB::beginTransaction();
                $data = $request->except('details');

                $attendance->update($data);

                // exists check
                $count = Attendance::where([
                    'date'              => $data['date'],
                    'institution_id'    => $data['institution_id'],
                    'campus_id'         => $data['campus_id'],
                    'medium_id'         => $data['medium_id'],
                    'group_id'          => $data['group_id'],
                    'section_id'        => $data['section_id'],
                    'shift_id'          => $data['shift_id'],
                    'academic_class_id' => $data['academic_class_id'],
                ])->count();
                if ($count == 2) {
                    DB::rollBack();
                    return response()->json(['error' => 'Sorry!! Already Submitted'], 200);
                }

                // update, delete, insert
                GlobalHelper::updelsert(
                    'attendance_id',
                    $attendance->id,
                    AttendanceDetails::class,
                    $request->details
                );

                DB::commit();
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        if ($attendance->delete()) {
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

    /**
     * Attendance Reports
     *
     * @return \Illuminate\Http\Response
     */
    public function attendanceReport(Request $request)
    {
        $from  = date('Y-m-d', strtotime($request->from_date));
        $to    = date('Y-m-d', strtotime($request->to_date));
        $field = $request->field_name ?? false;
        $value = $request->value ?? false;

        $query = DB::table('attendance_details as ad')
            ->join('attendances', 'attendances.id', '=', 'ad.attendance_id')
            ->join('students as std', 'std.software_id', '=', 'ad.software_id')
            ->join('student_profiles as pro', 'pro.student_id', '=', 'std.id')
            ->leftJoin('guardians as gr', 'gr.id', '=', 'std.guardian_id')
            ->selectRaw(
                'std.software_id,std.name_en,gr.name_en as guardian_name_en,gr.mobile as guardian_mobile,
                IFNULL(sum(CASE WHEN ad.status = "P" THEN 1 END),0) as total_present,
                0 as total_absent,
                0 as present_percentage'
            )
            ->whereDate('attendances.date', '>=', $from)
            ->whereDate('attendances.date', '<=', $to)
            ->where([
                'attendances.institution_id'    => $request->institution_id,
                'attendances.campus_id'         => $request->campus_id,
                'attendances.medium_id'         => $request->medium_id,
                'attendances.group_id'          => $request->group_id,
                'attendances.section_id'        => $request->section_id,
                'attendances.shift_id'          => $request->shift_id,
                'attendances.academic_class_id' => $request->academic_class_id,
            ])
            ->when($value, function ($q) use ($field, $value) {
                $q->where($field, 'LIKE', "%{$value}%");
            })
            // ->orderBy('total_present', 'desc')
            // ->orderBy('profile.roll_number', 'asc')
            ->groupBy('std.software_id');

        $data = $query->get();

        return $data;
    }

    /**
     * Attendance Sheet
     *
     * @return \Illuminate\Http\Response
     */
    public function attendanceSheet(Request $request)
    {
        $from       = date('Y-m-d', strtotime($request->from_date));
        $to         = date('Y-m-d', strtotime($request->to_date));
        $startDate  = Carbon::parse($request->from_date);
        $endDate    = Carbon::parse($request->to_date);
        $period     = CarbonPeriod::create($startDate, $endDate);

        $dates = [];
        foreach ($period as $singleDate) {
            $dates[$singleDate->format('Y-m-d')] = 'A';
        }

        $students = Student::select(
            'id',
            'software_id',
            'name_en'
        )->where([
            'institution_id'    => $request->institution_id,
            'campus_id'         => $request->campus_id,
            'medium_id'         => $request->medium_id,
            'group_id'          => $request->group_id,
            'section_id'        => $request->section_id,
            'shift_id'          => $request->shift_id,
            'academic_class_id' => $request->academic_class_id,
            'status'                    => 'active',
        ])->get()->toArray();


        foreach ($students as $stdKey => $student) {

            $presents = Attendance::where([
                'institution_id'    => $request->institution_id,
                'campus_id'         => $request->campus_id,
                'medium_id'         => $request->medium_id,
                'group_id'          => $request->group_id,
                'section_id'        => $request->section_id,
                'shift_id'          => $request->shift_id,
                'academic_class_id' => $request->academic_class_id,
            ])
                ->whereHas('details', function ($subquery) use ($student) {
                    $subquery->where('software_id', $student['software_id'])->where('status', 'P');
                })
                ->whereDate('date', '>=', $from)
                ->whereDate('date', '<=', $to)
                ->whereSub('details', 'status', 'P')
                ->pluck('date')
                ->toArray();

            $students[$stdKey]['attendance_data'] = $presents;
        }

        return ['students' => $students, 'dates' => $dates];
    }
}
