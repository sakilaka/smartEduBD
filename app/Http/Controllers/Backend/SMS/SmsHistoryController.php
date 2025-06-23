<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\SMS;

use App\Http\Resources\Resource;
use App\Models\SMS\SmsHistory;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SMS\SmsTemplate;
use App\Traits\SMSTrait;
use App\Models\Student;
use App\Models\OnlineAdmission;

class SmsHistoryController extends Controller
{
    use SMSTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query  = SmsHistory::latest('id')->with('sms_template');
        $query->whereLike($request->field_name, $request->value);
        $query->whereAny('sms_template_id', $request->sms_template_id);
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
                $template   = SmsTemplate::find($request->sms_template_id);
                $sms_body   = $template->sms_body;
                $sms_type   = $template->sms_type;
                $common_msg = $template->common_message;
                $send_type  = $request->sending_type;

                // Students
                if ($send_type != 'any') {

                    $students = $this->getStudentInfo($request);
                    if ($common_msg == 0) {
                        foreach ($students as $student) {
                            $params = [];
                            if (strpos($sms_body, '[_Password_]') !== false) {
                                $params['password'] = rand(111111, 999999);
                                $student->update(['password' => $params['password']]);
                            }

                            $this->sendSms($student->mobile, $sms_type, $params, $student);
                        }
                    } elseif (!empty($students) && $common_msg == 1) {
                        $contacts = $students->pluck('mobile')->toArray();
                        $contacts = implode(',', $contacts);
                        $this->sendSms($contacts, $sms_type);
                    }
                } else {
                    $contacts = trim($request->any_numbers);
                    $this->sendSms($contacts, $sms_type);
                }

                if ($this->insufficient_balance) {
                    return response()->json(['error' => 'Insufficient Balance!, Please recharge your SMS balance'], 200);
                }
                return response()->json(['message' => 'Send Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * get Students
     */
    private function getStudentInfo($request)
    {
        $studentsIds = $request->student_ids;
        $field_id = $request->sending_type == 'applicants' ? 'online_admission_id' : 'id';

        $students = Student::select('id', 'name', 'college_roll', 'mobile')
            ->when($studentsIds, function ($q) use ($studentsIds, $field_id) {
                $q->whereIn($field_id, $studentsIds);
            })
            ->where('status', 'active')
            ->where([
                'academic_session_id' => $request->academic_session_id,
                'institution_id' => $request->institution_id,
                'academic_qualification_id' => $request->academic_qualification_id,
                'academic_class_id' => $request->academic_class_id,
            ])->get();

        return $students;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SmsHistory  $smsHistory
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SmsHistory $smsHistory)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        return $smsHistory;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SmsHistory  $smsHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(SmsHistory $smsHistory)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SmsHistory  $smsHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SmsHistory $smsHistory)
    {
        if ($this->validateCheck($request, $smsHistory->id)) {
            try {
                $smsHistory->update($request->all());
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SmsHistory  $smsHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(SmsHistory $smsHistory)
    {
        if ($smsHistory->delete()) {
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
