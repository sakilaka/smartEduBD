<?php

namespace App\Services;

use App\Models\FeeSetup;
use App\Models\Invoice;
use App\Models\MasterSetup\AccountHead;
use App\Models\Student;
use App\Models\TuitionFeeGenerate;
use Exception;

class PaymentService
{
    /**
     * Get payment history
     *
     * @param object $student
     * @return \Illuminate\Http\Responses
     */
    public function paginate($student)
    {
        return Invoice::latest('id')
            ->join('account_heads', 'account_heads.id', '=', 'invoices.account_head_id')
            ->select(
                'invoices.id',
                'account_heads.name_en as account_head_name',
                'invoice_date',
                'invoice_number',
                'amount',
                'service_charge',
                'invoices.status'
            )
            ->withCount('details')
            ->where('student_id', $student->id)
            ->paginate(10);
    }

    /**
     * find specific invoice
     *
     * @param integer $student_id
     * @param integer $invoice_id
     * @return \Illuminate\Http\Responses
     */
    public function find($student_id, $invoice_id)
    {
        $invoice = Invoice::with(
            'details:invoice_id,invoice_date,account_head_id,amount',
            'details.head',
            'institution',
            'student',
            'student.profile:student_id,roll_number',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
            'head',
        )
            ->where('student_id', $student_id)
            ->where('id', $invoice_id)
            ->first();

        throw_unless($invoice, Exception::class, 'Invoice not found', 404);

        return $invoice;
    }

    /**
     * Pending invoice find
     *
     * @param integer $student_id
     * @param integer $invoice_id
     * @return \Illuminate\Http\Responses
     */
    public function pending($student_id, $invoice_id)
    {
        $invoice = Invoice::where('student_id', $student_id)
            ->where('id', $invoice_id)
            ->first();
        throw_unless($invoice, Exception::class, 'Invoice not found.', 404);

        $successInvoice = $invoice->status == 'success' ? true : false;
        throw_if($successInvoice, Exception::class, 'This invoice is already paid.', 400);

        $invoice->update(['created_from' => app('request_from')]);

        return $invoice;
    }

    /**
     * Get fees 
     *
     * @param \Illuminate\Http\Request $request
     * @param object $student
     * @return \Illuminate\Http\Responses
     */
    public function fees($request, $student)
    {
        $reqData = $request->only(['software_id', 'account_head_id', 'tuitions_id', 'bank_tran_id', 'payment_method']);
        $conditions = Student::conditions($student);
        $commonConditions = Student::commonArr($student);

        $head = AccountHead::find($reqData['account_head_id']);
        throw_unless($head, Exception::class, 'Account head not found.', 404);

        $feeSetup = FeeSetup::where($conditions)->first();
        $fees     = $feeSetup->details()->where('account_head_id', $reqData['account_head_id'])->first();
        throw_unless($fees, Exception::class, 'Fees not found, try again.', 404);

        $gateway = $fees->gateway->provider ?? "SSL";

        if ($head->type == 'tuition') {
            $request->validate(['tuitions_id' => 'required|array']);
            $fees = $this->getTuitionFees($student, $fees, $commonConditions, $reqData);
        } else {
            $fees = $this->getSchoolFees($student, $fees, $commonConditions);
        }

        $fees['created_from'] = app('request_from');
        $fees['invoice_type'] = $head->type;
        $fees['payment_method'] = $reqData['payment_method'] ?? $gateway;
        $fees['commonData'] = Student::commonArr($student);

        return $fees;
    }

    /**
     * Get school fees 
     *
     * @param object $student
     * @param array $reqData
     * @return \Illuminate\Http\Responses
     */
    private function getSchoolFees($student, $fees, $conditions)
    {
        /** check generate invoice **/
        $invoice = Invoice::where($conditions)
            ->where('student_id', $student->id)
            ->where('account_head_id', $fees->account_head_id)
            ->where('status', '!=', 'success')
            ->exists();
        throw_if($invoice, Exception::class, 'Already generate this payment type invoice, please check payment history.', 422);

        $discount = $student->discounts()->where([
            'account_head_id' => $fees->account_head_id,
            'academic_class_id' => $student->academic_class_id,
            'academic_session_id' => $student->academic_session_id,
            'institution_id' => $student->institution_id
        ])->value('amount') ?? 0;
        $fees['discount_amount'] = $discount;
        $fees['amount'] = $fees->amount - $discount;

        return $fees;
    }

    /**
     * Get tuition fees.
     *
     * @param object $student
     * @param object $fees
     * @param array $conditions
     * @param array $reqData
     * @return array
     * @throws Exception
     */
    private function getTuitionFees($student, $fees, $conditions, $reqData)
    {
        $tuitionConditions = [
            'shift_id'          => $conditions['shift_id'] ?? '',
            'group_id'          => $conditions['group_id'] ?? '',
            'medium_id'         => $conditions['medium_id'] ?? '',
            'institution_id'    => $conditions['institution_id'] ?? '',
            'academic_class_id' => $conditions['academic_class_id'] ?? '',
        ];
        $tuitionFees = TuitionFeeGenerate::where($tuitionConditions)->first();
        throw_unless($tuitionFees, Exception::class, 'Tuition fees not generated.', 422);

        $details = $tuitionFees->details()
            ->where('status', 1)
            ->whereIn('id', $reqData['tuitions_id'])
            ->orderBy('date', 'desc')
            ->get();
        throw_unless($details->count(), Exception::class, 'Tuition months not found.', 404);

        // Check if an invoice is already generated for the selected months
        $invoice = Invoice::where($conditions)
            ->join('invoice_details as details', 'details.invoice_id', 'invoices.id')
            ->where('invoices.student_id', $student->id)
            ->whereIn('details.tuition_fee_generate_details_id', $reqData['tuitions_id'])
            ->exists();
        throw_if($invoice, Exception::class, 'Already generate this tuition month invoice, please check payment history.', 422);


        // Check unpaid months
        if ($student->academic_class->institution_category_id != 3) {
            $this->validateUnpaidMonths($tuitionFees, $reqData['tuitions_id'], $details, $conditions, $student);
        }

        // Calculate discounts
        $discount = $student->discounts()->where([
            'account_head_id' => $fees->account_head_id,
            'academic_class_id' => $student->academic_class_id,
            'academic_session_id' => $student->academic_session_id,
            'institution_id' => $student->institution_id
        ])->value('amount') ?? 0;
        $discountAmount = $discount * $details->count();

        $fees['discount_amount'] = $discountAmount;
        $fees['amount'] = $details->sum('amount') - $discountAmount;
        $fees['details'] = $details;

        return $fees;
    }

    /**
     * Validate unpaid tuition months.
     *
     * @param object $tuitionFees
     * @param array $tuitionIDs
     * @param object $details
     * @param array $conditions
     * @param object $student
     * @throws Exception
     */
    private function validateUnpaidMonths($tuitionFees, $tuitionIDs, $details, $conditions, $student)
    {
        // Get IDs of previous months that are not in the current selection
        $previousMonthIDs = $tuitionFees->details()
            ->where('status', 1)
            ->whereNotIn('id', $tuitionIDs)
            ->whereDate('date', '<', $details->first()->date)
            ->pluck('id')
            ->toArray();

        // Get IDs of paid months
        $paidMonthIDs = Invoice::where($conditions)
            ->join('invoice_details as details', 'details.invoice_id', '=', 'invoices.id')
            ->where('invoices.student_id', $student->id)
            ->where('details.status', 'success')
            ->whereNotNull('details.tuition_fee_generate_details_id')
            ->pluck('details.tuition_fee_generate_details_id')
            ->toArray();

        // Find unpaid months
        $unpaidMonthIDs = array_diff($previousMonthIDs, $paidMonthIDs);

        if (!empty($unpaidMonthIDs)) {
            $unpaidMonths = $tuitionFees->details()
                ->whereIn('id', $unpaidMonthIDs)
                ->pluck('date')
                ->map(fn($date) => \Carbon\Carbon::parse($date)->format('F'))
                ->toArray();

            throw new Exception("You have to pay previous month dues: " . implode(', ', $unpaidMonths), 400);
        }
    }
}
