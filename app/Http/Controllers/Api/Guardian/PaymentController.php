<?php

namespace App\Http\Controllers\Api\Guardian;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\FeeSetupDetails;
use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Models\Student;
use App\Services\Gateway\PaymentGatewayService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $student = auth()->user()->current_student;
        $data = $this->paymentService->paginate($student);

        return new Resource($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentGatewayService $paymentGateway, Request $request)
    {
        $request_from = app('request_from');
        $request->validate(['account_head_id' => 'required']);

        $exists = $this->isDependentPayments($request->id);
        if ($exists['status']) {
            return $this->sendError("You have to pay {$exists['head']} first {$exists['status']}", 400);
        }

        try {
            DB::beginTransaction();

            $student = auth()->user()->current_student;

            // create invoice
            $fees = $this->paymentService->fees($request, $student);
            $invoice = Invoice::store($student->id, $fees);

            DB::commit();

            $response = $paymentGateway->initiate($invoice['id'], $fees->gateway);

            if (!empty($response['invoice_number']) && !empty($response['order_id'])) {
                $invoice->update(['gateway_order_id' => $response['order_id']]);
            }

            if ($request_from == 'app') {
                return $this->sendResponse($response['checkout_url'] ?? '', 200, "Please pay using your preferred payment method");
            }
            return $this->sendResponse($response, 200, "Please pay using your preferred payment method");
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Pay Invoice
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function payInvoice(PaymentGatewayService $paymentGateway, Request $request)
    {
        $request_from = app('request_from');
        $request->validate(['invoice_id' => 'required']);

        try {
            $student = auth()->user()->current_student;
            $invoice = $this->paymentService->pending($student->id, $request->invoice_id);
            $gateway = PaymentGateway::find($invoice->payment_gateway_id);

            // update service charge
            $service_charge = Invoice::serviceCharge($invoice->institution_id, $gateway->provider, $invoice->amount);
            $invoice->update([
                'service_charge' => $service_charge,
                'payment_method' => $gateway->provider
            ]);

            $response = $paymentGateway->initiate($invoice->id, $gateway);

            if (!empty($response['invoice_number']) && !empty($response['order_id'])) {
                $invoice->update(['gateway_order_id' => $response['order_id']]);
            }

            if ($request_from == 'app') {
                return $this->sendResponse($response['checkout_url'] ?? '', 200, "Please pay using your preferred payment method");
            }
            return $this->sendResponse($response, 200, "Please pay using your preferred payment method");
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Download invoice / invoice data
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function invoice(Request $request)
    {
        $request->validate(['invoice_id' => 'required']);
        try {
            $student = auth()->user()->current_student;
            $invoice = $this->paymentService->find($student->id, $request->invoice_id);

            if (empty($request->pdf)) {
                return $this->sendResponse($invoice);
            }

            $data = [
                'invoice'   => $invoice,
                'logo'      => $invoice->institution->logo ?? null,
                'paid_logo' => public_path('images/paid.png'),
            ];

            $pdf = PDF::loadView('pdf.invoice', $data)->setPaper('a4', 'portrait');
            return $pdf->download("invoice.pdf");
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Check Dependent Head
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkDependentHead(Request $request)
    {
        $exists = $this->isDependentPayments($request->id);
        if ($exists['status']) {
            return $this->sendError("You have to pay {$exists['head']} first", 400);
        }
        return $this->sendResponse([], 200, "You can pay this fee");
    }

    private function isDependentPayments($fees_details_id)
    {
        $status = true;
        $dependHead = "----";
        $student = auth()->user()->current_student;
        $conditions = Student::commonArr($student);
        $fees       = FeeSetupDetails::whereNotNull('depend_head_id')->find($fees_details_id);

        if (!empty($fees)) {
            $dependHead = $fees->depend_head->name_en ?? "----";
            $status     = Invoice::where($conditions)
                ->where('student_id', $student->id)
                ->where('account_head_id', $fees->depend_head_id)
                ->where('status', 'success')
                ->exists();
        }
        return [
            'status' => !$status ? true : false,
            'head' => $dependHead
        ];
    }
}
