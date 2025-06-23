<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BaseController;
use App\Http\Resources\Resource;
use App\Models\Invoice;
use App\Models\PaymentGateway;
use Exception;
use Illuminate\Http\Request;
use App\Helpers\GlobalHelper;
use App\Models\MasterSetup\Institution;
use App\Models\Student;
use App\Services\FeesService;

class InvoiceController extends BaseController
{
    // use AccountSummary;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $insID = Institution::current() ?? $request->institution_id;

        $query = Invoice::select(
            'id',
            'institution_id',
            'academic_session_id',
            'student_id',
            'account_head_id',
            'campus_id',
            'shift_id',
            'medium_id',
            'academic_class_id',
            'group_id',
            'section_id',
            'invoice_date',
            'invoice_number',
            'amount',
            'payment_date',
            'payment_method',
            'created_from',
            'service_charge',
            'status',
        )
            ->withCount('details')
            ->with(
                'institution',
                'student',
                'academic_session',
                'campus',
                'shift',
                'medium',
                'academic_class',
                'group',
                'section',
                'head',
            )
            ->where('amount', '!=', 0)
            ->latest('id');

        $query->whereDates('payment_date', $request->from_date, $request->to_date);
        $query->whereAny('institution_id', $insID);
        $query->whereSub('academic_class', 'institution_category_id', $request->institution_category_id);
        $query->whereAny('academic_session_id', $request->academic_session_id);
        $query->whereAny('campus_id', $request->campus_id);
        $query->whereAny('shift_id', $request->shift_id);
        $query->whereAny('medium_id', $request->medium_id);
        $query->whereAny('academic_class_id', $request->academic_class_id);
        $query->whereAny('group_id', $request->group_id);
        $query->whereAny('section_id', $request->section_id);
        $query->whereAny('account_head_id', $request->account_head_id);
        $query->whereAny('payment_method', $request->payment_method);
        $query->whereAny('created_from', $request->created_from);

        if (in_array($request->field_name, ['invoice_number'])) {
            $query->whereLike($request->field_name, $request->value);
        } else if (in_array($request->field_name, ['roll_number'])) {
            $query->whereSubLike('profile', $request->field_name, $request->value);
        } else if (in_array($request->field_name, ['guardian_mobile'])) {
            $query->whereSubLike('student.guardian', 'mobile', $request->value);
        } else {
            $query->whereSubLike('student', $request->field_name, $request->value);
        }
        if ($request->service_charge) {
            $query->where('service_charge', '>', 0);
        }

        if (!empty($request->payment_gateway_id)) {
            $payGateway = PaymentGateway::find($request->payment_gateway_id);
            $gateways   = PaymentGateway::where('store_id', $payGateway->store_id)->pluck('id');

            $query->whereIn('payment_gateway_id', $gateways);
        }

        if ($request->status == 'pending') {
            $query->where('status', '!=', 'success');
        } else {
            $query->whereAny('status', $request->status);
        }

        $datas = $query->paginate($request->pagination);
        return new Resource($datas);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountWise()
    {
        return view('layouts.backend_app');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accountHeadWise()
    {
        return view('layouts.backend_app');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tuitionFeesDues(Request $request)
    {
        $feesService = app(FeesService::class);
        $institution_id = Institution::current() ?? $request->institution_id;

        // dues Student
        $studentQuery = Student::with(
            'profile:student_id,roll_number,profile',
            'guardian',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
        )->select(
            'id',
            'institution_id',
            'campus_id',
            'shift_id',
            'medium_id',
            'academic_class_id',
            'group_id',
            'section_id',
            'software_id',
            'name_en',
            'mobile',
            'email',
        );

        $studentQuery->whereAny('institution_id', $institution_id);
        $studentQuery->whereAny('campus_id', $request->campus_id);
        $studentQuery->whereAny('shift_id', $request->shift_id);
        $studentQuery->whereAny('medium_id', $request->medium_id);
        $studentQuery->whereAny('academic_class_id', $request->academic_class_id);
        $studentQuery->whereAny('group_id', $request->group_id);
        $studentQuery->whereAny('section_id', $request->section_id);
        $studentQuery->whereLike($request->field_name, $request->value);
        $studentQuery->where('status', 'active');

        $data = $studentQuery->get();
        $data = $data->transform(function ($student) use ($request, $feesService) {
            $duesQuery = $feesService->tuitionDuesFees($student);

            if ($request->dues_month) {
                $selectedMonth = (int)$request->dues_month;
                $duesQuery = $duesQuery->filter(function ($item) use ($selectedMonth) {
                    if (!isset($item->date)) {
                        return false;
                    }
                    $itemMonth = \Carbon\Carbon::parse($item->date)->month;
                    // Include all months from January (1) up to the selected month
                    return $itemMonth >= 1 && $itemMonth <= $selectedMonth;
                });
            }

            $duesAmount = $duesQuery->sum('amount');
            $student->total_dues = $duesAmount;
            $student->dues_fees = $duesQuery;
            return $student;
        })->filter(function ($student) {
            return $student->total_dues > 0;
        });

        return new Resource($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function refundAmount(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $query = Invoice::with(
            'department',
            'qualification',
            'academic_class',
            'head',
            'student:id,student_id,name,mobile,admission_id,reg_no',
        )->where('refund_amount', '!=', 0)->latest('id');

        if ($request->field_name == 'invoice_number') {
            $query->whereLike($request->field_name, $request->value);
        } else {
            if ($request->field_name == 'admission_id') {
                $query->whereSub('student', $request->field_name, $request->value);
            } else {
                $query->whereSubLike('student', $request->field_name, $request->value);
            }
        }

        $query->whereDates('payment_date', $request->from_date, $request->to_date);
        $query->whereAny('academic_class_id', $request->academic_class_id);
        $query->whereAny('academic_qualification_id', $request->academic_qualification_id);
        $query->whereAny('academic_session_id', $request->academic_session_id);
        $query->whereAny('institution_id', $institution_id);

        $datas = $query->paginate($request->pagination);
        return new Resource($datas);
    }

    /**
     * Departmentwise Payment
     *
     * @return \Illuminate\Http\Response
     */
    public function departmentWise(Request $request)
    {
        // $query = Department::latest()->active();
        // $query->withCount([
        //     'invoice AS total_payment' => function ($query) {
        //         $query->select(DB::raw("SUM(amount) as sum"))->where('status', 'success');
        //     },
        // ]);
        // $query->withCount(['invoice' => function ($query) {
        //     $query->where('status', 'success');
        // }]);

        // $data = $query->paginate($request->pagination);
        // return new Resource($data);
    }

    /**
     * Account Summary
     *
     * @return \Illuminate\Http\Response
     */
    public function accountSummary(Request $request)
    {
        $report_type = $request->report_type;

        switch ($report_type) {
            case 'invoice':
                $summary = $this->invoiceSummary($request);
                break;

            case 'admission':
                $summary = $this->admissionSummary($request);
                break;

            case 'hostel':
                $summary = $this->hostelSummary($request);
                break;

            case 'all':
                $summary = $this->allSummary($request);
                break;

            default:
                $summary = [];
                break;
        }

        return $summary;
    }

    /**
     * Service Charge
     *
     * @return \Illuminate\Http\Response
     */
    public function serviceCharge(Request $request)
    {
        // For invoice charge
        $invoiceQuery = Invoice::where('service_charge', '>', 0);
        $invoiceQuery->whereDates('payment_date', $request->from_date, $request->to_date);
        $invoiceQuery->whereAny('academic_class_id', $request->academic_class_id);
        $invoiceQuery->whereAny('academic_qualification_id', $request->academic_qualification_id);
        $invoiceQuery->whereAny('academic_session_id', $request->academic_session_id);
        $invoiceQuery->whereAny('department_id', $request->department_id);
        $invoiceQuery->whereAny('account_head_id', $request->account_head_id);
        $invoiceQuery->where('status', 'success');
        if (!empty($request->payment_gateway_id)) {
            $payGateway = PaymentGateway::find($request->payment_gateway_id);
            $gateways   = PaymentGateway::where('store_id', $payGateway->store_id)->pluck('id');
            $invoiceQuery->whereIn('payment_gateway_id', $gateways);
        }
        $invoiceQuery->selectRaw('SUM(service_charge) as total_service_charge');
        $data = $invoiceQuery->first();

        return $data;
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
        try {
            $data = $request->all();

            $data['invoice_number'] = GlobalHelper::generate_id(Invoice::class, 'invoice_number', [
                'pad_length' => 5,
                'prefix'     => "INV-",
            ]);

            $data['invoice_date'] = date("Y-m-d");
            $data['status'] = "pending";
            $data['created_from'] = "admin";

            Invoice::create($data);

            return response()->json(['message' => 'Create Successfully!'], 200);
        } catch (Exception $ex) {
            return response()->json(['exception' => $ex->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Invoice $invoice)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        $invoice->load([
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
        ]);
        return $invoice;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($request->status == 'success' && $invoice->status != 'success') {
            Invoice::paymentSuccessFromAdmin($request);
        } elseif ($request->status == 'success' && $invoice->status == 'success') {
            Invoice::successInvoiceModifyFromAdmin($request);
        }

        return response()->json(['message' => 'Update Successfully!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->details()->delete();
        if ($invoice->delete()) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['message' => 'Delete Unsuccessfully!'], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function invoicePrint(Request $request)
    {
        $insID        = auth()->guard('admin')->user()->institution_id;
        $institution_id = !empty($request->department) ? $request->department : $insID;

        $query = Invoice::with('head', 'student', 'online_admission', 'academic_session', 'department', 'qualification', 'academic_class');
        $query->whereDates('payment_date', $request->from_date, $request->to_date);
        $query->whereAny('academic_class_id', $request->class);
        $query->whereAny('academic_qualification_id', $request->qualification);
        $query->whereAny('academic_session_id', $request->session);
        $query->whereAny('institution_id', $institution_id);
        $query->whereSub('student', 'student_type', $request->student_type);
        $query->whereAny('account_head_id', $request->account_head);
        $query->whereAny('payment_gateway_id', $request->gateway);
        $query->whereAny('status', $request->status);

        $invoices = $query->get();
        return response()->json($invoices);
    }
}
