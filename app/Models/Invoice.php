<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\MasterSetup\AccountHead;
use App\Models\Base\ParentModel;
use App\Models\MasterSetup\Group;
use App\Models\MasterSetup\Section;
use App\Models\MasterSetup\Medium;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\AcademicSession;
use App\Models\MasterSetup\Shift;
use App\Models\MasterSetup\Campus;
use App\Models\MasterSetup\Institution;

class Invoice extends ParentModel
{
    protected $guarded = ['id'];

    protected static $logName = 'Invoice';

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Relations with others table
     *
     * @var array
     */
    public function details()
    {
        return $this->hasMany(InvoiceDetails::class, 'invoice_id');
    }
    public function head()
    {
        return $this->hasOne(AccountHead::class, 'id', 'account_head_id')->select('id', 'name_bn', 'name_en', 'type');
    }
    public function academic_session()
    {
        return $this->belongsTo(AcademicSession::class)->select('id', 'name', 'institution_category_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class)->select('id', 'name_en', 'guardian_id', 'software_id', 'academic_session_id');
    }
    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name', 'logo');
    }
    public function campus()
    {
        return $this->belongsTo(Campus::class)->select('id', 'name');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class)->select('id', 'name');
    }
    public function medium()
    {
        return $this->belongsTo(Medium::class)->select('id', 'name');
    }
    public function academic_class()
    {
        return $this->belongsTo(AcademicClass::class)->select('id', 'name');
    }
    public function group()
    {
        return $this->belongsTo(Group::class)->select('id', 'name');
    }
    public function section()
    {
        return $this->belongsTo(Section::class)->select('id', 'name');
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store(int $studentID, $fees)
    {
        $institution_id = $fees['commonData']['institution_id'] ?? '';
        $payment_method = $fees->payment_method ?? "SSL";
        $service_charge = self::serviceCharge($institution_id, $payment_method, $fees->amount);

        $arr = [
            'invoice_date'              => date("Y-m-d"),
            'invoice_number'            => self::invoiceNumber(),
            'student_id'                => $studentID,
            'account_head_id'           => $fees->account_head_id,
            'payment_gateway_id'        => $fees->payment_gateway_id,
            'invoice_type'              => $fees->invoice_type,
            'discount_amount'           => $fees->discount_amount,
            'amount'                    => $fees->amount,
            'service_charge'            => $service_charge,
            'payment_method'            => $payment_method,
            'created_from'              => $fees->created_from ?? "web",
            'status'                    => "pending"
        ];

        $arr += $fees['commonData'];

        $invoice = Invoice::create($arr);

        if (!empty($fees->details)) {
            foreach ($fees->details as $details) {
                $invoice->details()->create([
                    'account_head_id' => $invoice->account_head_id,
                    'tuition_fee_generate_details_id' => $details['id'],
                    'amount'          => $details['amount'],
                    'invoice_date'    => $details['date']
                ]);
            }
        }

        return $invoice;
    }

    /**
     * GET PAYMENT SUCCESS STATUS FROM SSL
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function paymentSuccess($request)
    {
        $invoice = Invoice::where('invoice_number', $request['tran_id'] ?? '')->first();

        // -----------invoice update------------
        $data['status']        = 'success';
        $data['bank_trans_id'] = $request['bank_tran_id'] ?? "";
        $data['card_type']     = $request['card_type'] ?? "";
        $data['payment_method'] = $request['payment_method'] ?? "SSL";
        $data['payment_date']  = date('Y-m-d');
        $data['paid_amount']   = $invoice->amount ?? "";
        $res = $invoice->update($data);

        if (!empty($invoice->details)) {
            $invoice->details()->update(['status' => 'success']);
        }

        return $res;
    }

    /**
     * MANUALLY PAYMENT SUCCESS STATUS FROM ADMIN
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function paymentSuccessFromAdmin($request)
    {
        $invoice  = Invoice::where('id', $request->id)->first();

        // -----------invoice update------------
        $data['status']        = 'success';
        $data['bank_trans_id'] = $invoice->bank_trans_id ?? "manually/" . date('Y-m-d');
        $data['card_type']     = $invoice->card_type ?? "BKASH-BKash";
        $data['payment_date']  = $request->payment_date ?? date('Y-m-d');
        $data['amount']        = $request->amount ?? $invoice->amount;
        $data['paid_amount']   = $request->amount ?? $invoice->amount;
        $res = $invoice->update($data);

        if (!empty($invoice->details)) {
            $invoice->details()->update(['status' => 'success']);
        }

        return $res;
    }

    /**
     * SUCCESS INVOICE MODIFY FROM ADMIN
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function successInvoiceModifyFromAdmin($request)
    {
        $data = [
            'amount' => $request->amount,
            'account_head_id' => $request->account_head_id,
            'status' => $request->status,
            'payment_date' => $request->payment_date,
        ];

        $invoice = Invoice::where('id', $request->id)->first();
        $invoice->update($data);

        return response()->json(true);
    }

    /**
     * Fenerate invoice number
     *
     * @return integer
     */
    public static function invoiceNumber()
    {
        $invoice_number = Invoice::latest('invoice_number')->value('invoice_number');
        return empty($invoice_number) ? 100 : $invoice_number + 1;
    }


    // Calculate service charge
    public static function serviceCharge($institution_id, $method, $amount)
    {
        $field = $method == 'SSL' ? 'ssl_service_charge_percent' : 'spay_service_charge_percent';
        $institution = Institution::where('id', $institution_id)->first();
        $charge = ((float) $amount * $institution[$field]) / 100;
        return round($charge, 2);
    }
}
