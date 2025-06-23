<?php

namespace App\Http\Controllers\Api\Bank;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'software_id' => 'required',
            'account_head_id' => 'required|array',
            'bank_tran_id' => 'required|unique:invoices,bank_trans_id'
        ]);
        $reqData = $request->only(['software_id', 'account_head_id', 'tuitions_id', 'bank_tran_id']);

        try {
            DB::beginTransaction();

            $student = Student::where(['software_id' => $reqData['software_id']])->first();
            throw_unless($student, NotFoundHttpException::class, 'Student not found.');

            foreach ($reqData['account_head_id'] ?? [] as $head_id) {
                $reqData['account_head_id'] = $head_id;
                $reqData['payment_method'] = 'DBL';

                // create invoice
                $fees = $this->paymentService->fees(new Request($reqData), $student);

                $invoice = Invoice::store($student->id, $fees);
                throw_unless($invoice, \Exception::class, 'Something went wrong, please try again');

                // payment success
                $reqData['card_type'] = 'DHAKA-BANK';
                $reqData['tran_id'] = $invoice['invoice_number'] ?? '';
                Invoice::paymentSuccess($reqData);
            }

            DB::commit();

            $result = $this->invoiceInfo($student->id, $reqData);
            return $this->sendResponse($result, 200, "Payment successfully");
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Payment status check
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function paymentStatus(Request $request)
    {
        $request->validate([
            'software_id' => 'required',
            'bank_tran_id' => 'required'
        ]);
        $reqData = $request->only(['software_id', 'bank_tran_id']);

        $student = Student::where(['software_id' => $reqData['software_id']])->first();
        throw_unless($student, NotFoundHttpException::class, 'Student not found.');

        $invoices = Invoice::where([
            'student_id' => $student->id,
            'bank_trans_id' => $reqData['bank_tran_id'],
            'created_from' => 'dbank'
        ])->get();
        throw_unless($invoices->count(), NotFoundHttpException::class, 'Invoice not found.');

        $invoice = $this->invoiceInfo($student->id, $reqData);
        return $this->sendResponse($invoice);
    }

    /**
     * Payment reverse
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function paymentReverse(Request $request)
    {
        $request->validate([
            'software_id' => 'required',
            'bank_tran_id' => 'required'
        ]);

        $reqData = $request->only(['software_id', 'bank_tran_id']);

        try {
            DB::beginTransaction();

            $student = Student::where(['software_id' => $reqData['software_id']])->first();
            throw_unless($student, NotFoundHttpException::class, 'Student not found.');

            $invoices = Invoice::where([
                'student_id' => $student->id,
                'bank_trans_id' => $reqData['bank_tran_id'],
                'created_from' => 'dbank'
            ])->get();
            throw_unless($invoices->count(), NotFoundHttpException::class, 'Invoice not found.');

            foreach ($invoices as $key => $invoice) {
                if (!empty($invoice->details)) {
                    $invoice->details()->delete();
                }
                $invoice->delete();
            }

            DB::commit();
            return $this->sendResponse([], 200, 'Reverse successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Payment retry
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function paymentRetry(Request $request)
    {
        $request->validate([
            'software_id' => 'required',
            'bank_tran_id' => 'required'
        ]);

        $reqData = $request->only(['software_id', 'bank_tran_id']);

        try {
            $student = Student::where(['software_id' => $reqData['software_id']])->first();
            throw_unless($student, NotFoundHttpException::class, 'Student not found.');

            $invoices = Invoice::where([
                'student_id' => $student->id,
                'bank_trans_id' => $request->bank_tran_id,
                'created_from' => 'dbank'
            ])->get();
            throw_unless($invoices->count(), NotFoundHttpException::class, 'Invoice not found.');

            $paymentStatus = $invoices->where('status', '!=', 'success')->count();
            throw_unless($paymentStatus, NotFoundHttpException::class, 'This invoice is already paid.');

            foreach ($invoices as $key => $invoice) {
                $reqData['card_type'] = 'DHAKA-BANK';
                $reqData['tran_id'] = $invoice->invoice_number;
                Invoice::paymentSuccess($reqData);
            }

            $result = $this->invoiceInfo($student->id, $reqData);
            return $this->sendResponse($result);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * invoice info
     *
     * @param $student_id
     * @param $reqData
     * @return \Illuminate\Http\Response
     */
    private function invoiceInfo($student_id, $reqData)
    {
        $result = $this->invoiceQuery($student_id, $reqData)->first()->toArray();

        $invoice = Invoice::where(['student_id' => $student_id, 'bank_trans_id' => $reqData['bank_tran_id']]);

        $result['amount'] = $invoice->sum('amount');
        $result['discount_amount'] = $invoice->sum('discount_amount');
        $result['status'] = $invoice->where('status', '!=', 'success')->exists() ? 'failed' : 'success';

        $result['invoices'] = $this->invoiceQuery($student_id, $reqData)
            ->with('details:invoice_id,amount,invoice_date')
            ->addSelect('account_heads.name_en as account_head_name')
            ->get();
        return $result;
    }

    private function invoiceQuery($student_id, $reqData)
    {
        return Invoice::join('account_heads', 'account_heads.id', '=', 'invoices.account_head_id')
            ->join('students', 'students.id', '=', 'invoices.student_id')
            ->join('student_profiles as prof', 'prof.student_id', 'invoices.student_id')
            ->select(
                'invoices.id',
                'students.software_id',
                'students.name_en as student_name',
                'prof.fathers_name_en',
                'prof.mothers_name_en',
                'amount',
                'discount_amount',
                'bank_trans_id',
                'invoices.status',
            )
            ->where([
                'invoices.student_id' => $student_id,
                'bank_trans_id' => $reqData['bank_tran_id'],
                'invoices.created_from' => 'dbank'
            ]);
    }
}