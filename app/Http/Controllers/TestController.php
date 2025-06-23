<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\MasterSetup\AcademicSession;
use App\Models\PaymentGateway;
use App\Models\Result\SecondaryResult;
use App\Models\Result\SecondaryResultDetails;
use App\Models\Student;
use App\Models\StudentDiscount;
use App\Services\Gateway\PaymentGatewayService;
use App\Traits\SMSTrait;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use SMSTrait;


    /**
     * Initiate the ShurjoPay payment process.
     * 
     * @param Request $request 
     * @param ShurjoPayGateway $spay 
     * @return \Illuminate\Http\JsonResponse
     */
    function makeSpay(PaymentGatewayService $paymentGateway, Request $request)
    {
        $gateway = PaymentGateway::find(19);
        $payinfo = $paymentGateway->initiate(15569, $gateway);
        return response()->json($payinfo);
    }

    public function sendSmsToTest(Request $request)
    {
        if (empty($request->mobile)) return "Give mobile number";

        $message = "Dear Guardian,\nPlease pay Police Lines School & College Tuition Fees of January,February and March within 21-03-24.\n\nApp download link: https://shorturl.at/guzCM\n\nStudent ID: {111111}\nUser ID: {$request->mobile}\nPass: abc123\n\nYou can pay Dhaka Bank Branch also.\n\nHotline: 01710409659 (PLSCF).";
        $this->sendSms($request->mobile, $message);
        return "Send successfully";
    }

    public function sessionUpdate(Request $request)
    {
        return false;
        // Validate confirmation
        $request->validate([
            'confirm' => 'required|in:ok'
        ], [
            'confirm.in' => 'Please confirm to update your session.'
        ]);

        // Update student discounts
        $discounts = StudentDiscount::select('id', 'student_id')
            ->whereNull('academic_session_id')
            ->oldest('id')
            ->with('student:id,academic_session_id')
            ->get();

        $updateData = [];
        foreach ($discounts as $discount) {
            if (empty($discount->student) || empty($discount->student->academic_session_id)) continue;

            $updateData[] = [
                'id' => $discount->id,
                'academic_session_id' => $discount->student->academic_session_id
            ];
        }

        if (!empty($updateData)) {
            $chunks = array_chunk($updateData, 1000);
            foreach ($chunks as $chunk) {
                StudentDiscount::upsert($chunk, ['id'], ['academic_session_id']);
            }
        }

        // Update invoices
        $invoices = Invoice::select('id', 'student_id')
            ->whereNull('academic_session_id')
            ->oldest('id')
            ->with('student:id,academic_session_id')
            ->get();

        $updateInvoices = [];
        foreach ($invoices as $invoice) {
            if (empty($invoice->student) || empty($invoice->student->academic_session_id)) continue;

            $updateInvoices[] = [
                'id' => $invoice->id,
                'academic_session_id' => $invoice->student->academic_session_id
            ];
        }

        if (!empty($updateInvoices)) {
            $chunks = array_chunk($updateInvoices, 1000);
            foreach ($chunks as $chunk) {
                Invoice::upsert($chunk, ['id'], ['academic_session_id']);
            }
        }

        return "Session updated successfully.";
    }


    /**
     * Delete Institutions
     *
     * @param Request $request 
     * @return void
     */
    public function deleteInstitutions(Request $request)
    {
        return false;
        if ($request->confirm != 'ok') {
            return "Please confirm to delete";
        }
        $guardianIDs = Student::where('institution_id', 40)->groupBy('guardian_id')->pluck('guardian_id')->toArray();

        Student::where('institution_id', 40)->forceDelete();
        Guardian::whereIn('id', $guardianIDs)->forceDelete();
        return "Delete successfully";
    }

    /**
     * Delete Result
     *
     * @param Request $request 
     * @return void
     */
    public function deleteResult(Request $request)
    {
        return false;
        if ($request->confirm != 'ok') {
            return "Please confirm to delete";
        }

        // delete result
        SecondaryResult::where('id', 51)->delete();

        // delete student
        Student::where([
            'institution_id' => 7,
            'academic_session_id' => 5,
            'campus_id' => 10,
            'medium_id' => 1,
            'group_id' => 1,
            'section_id' => 2,
            'shift_id' => 2,
            'academic_class_id' => 10
        ])->forceDelete();

        return "Delete successfully";
    }



    /**
     * Delete Result
     *
     * @param Request $request 
     * @return void
     */
    public function syncResultRoll(Request $request)
    {
        if ($request->confirm != 'ok') {
            return "Please confirm to sync roll number";
        }

        $results = SecondaryResult::with('details')->get();
        foreach ($results as $result) {
            $details_student_ids = $result->details->pluck('student_id')->toArray();

            $students = Student::join('student_profiles as pro', 'students.id', '=', 'pro.student_id')
                ->select(
                    'students.id',
                    'students.name_en',
                    'pro.roll_number',
                    'students.software_id',
                )
                ->whereIn('students.id', $details_student_ids)
                ->where([
                    'institution_id'    => $result->institution_id,
                    'academic_session_id' => $result->academic_session_id,
                    'campus_id'         => $result->campus_id,
                    'shift_id'          => $result->shift_id,
                    'medium_id'         => $result->medium_id,
                    'academic_class_id' => $result->academic_class_id,
                    'group_id'          => $result->group_id,
                    'section_id'        => $result->section_id,
                ])
                ->pluck('pro.roll_number', 'students.id')
                ->toArray();

            // generate students roll_numbers
            $student_roll_numbers = [];
            foreach ($result->details as $detail) {
                if (!empty($students[$detail->student_id])) {
                    array_push($student_roll_numbers, [
                        'id' => $detail->id,
                        'roll_number' => $students[$detail->student_id]
                    ]);
                }
            }
            // dd($student_roll_numbers);

            SecondaryResultDetails::upsert(
                $student_roll_numbers,
                ['id'],
                ['roll_number']
            );
        }

        return "Result sync successfully";
    }
}
