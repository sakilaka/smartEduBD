<?php

namespace App\Http\Controllers\Backend\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\FeeSetup;
use App\Models\MasterSetup\Institution;
use App\Services\SSLCommerz\SslCommerzPayment;
use App\Models\Student;
use App\Services\FeesService;
use App\Services\Gateway\PaymentGatewayService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

class StudentPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function student(Request $request, FeesService $feesService)
    {
        $institution_id = Institution::current() ?? '';

        $student = Student::with(
            'profile:student_id,fathers_name_en,mothers_name_en',
            'shift',
            'campus',
            'group',
            'medium',
            'section',
            'academic_class',
        )
            ->select('id', 'institution_id', 'academic_session_id', 'software_id', 'campus_id', 'shift_id', 'medium_id', 'academic_class_id', 'group_id', 'section_id', 'name_en')
            ->where('software_id', $request->software_id)
            ->whereAny('institution_id', $institution_id)
            ->first();

        if (empty($student)) {
            return response()->json(['error' => "Student not found"], 201);
        }

        $discounts = [];
        if (!empty($student->discounts)) {
            $discounts = $student->discounts()->where('academic_session_id', $student->academic_session_id)->get();
        }

        $data['student_info'] = $student;
        $data['fees_lists'] = $feesService->dues($student);
        $data['discounts']  = $discounts;

        return $data;
    }

    /**
     * Student payment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function payment(PaymentGatewayService $paymentGateway, Request $request)
    {
        try {
            DB::beginTransaction();
            $student  = Student::find($request->student_id);
            if (empty($student)) {
                return response()->json(['error' => "Student not found"], 201);
            }

            $conditions = Student::conditions($student);
            $discount = $request->discount_amount ?? 0;

            // Fees Amount
            $feeSetup = FeeSetup::where($conditions)->first();
            $fees     = $feeSetup->details()->where('account_head_id', $request->account_head_id)->first();

            // If fees not found
            if (empty($fees)) {
                return response()->json(['error' => "Sorry!! You cannot select any payment type"], 201);
            }

            // Check generate invoice
            $invoice = Invoice::where($conditions)
                ->where('student_id', $student->id)
                ->where('account_head_id', $fees->account_head_id)
                ->where('status', '!=', 'success')
                ->first();

            // If invoice is not created then create Invoice
            if (!is_object($invoice)) {
                $fees['commonData'] = Student::commonArr($student);
                $fees['created_from'] = 'admin';
                $fees['invoice_type'] = 'school';
                $fees['discount_amount'] = $discount;
                $fees['amount'] = $fees->amount - $discount;

                $invoice = Invoice::store($student->id, $fees);
            } else {
                $invoice->update([
                    'created_from' => 'admin',
                    'discount_amount' => $discount,
                    'amount' => $fees->amount - $discount
                ]);
            }

            $response = $paymentGateway->initiate($invoice->id, $fees->gateway);
            if (!empty($response['invoice_number']) && !empty($response['order_id'])) {
                $invoice->update(['gateway_order_id' => $response['order_id']]);
            }

            DB::commit();
            return $this->sendResponse($response, 200, "Please pay using your preferred payment method");
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['exception' => $ex->getMessage()], 422);
        }
    }
}
