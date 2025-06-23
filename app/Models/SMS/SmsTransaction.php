<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\SMS;

use App\Models\Base\BaseModel;
use App\Helpers\GlobalHelper;
use App\Models\System\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SmsTransaction extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Sms Transaction";

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store($req)
    {
        $invoiceNumber = GlobalHelper::generate_id(SmsTransaction::class, 'invoice_number', [
            'pad_length' => 5,
            'prefix'     => "SMS-",
        ]);

        $arr = [
            'invoice_date'   => date("Y-m-d"),
            'invoice_number' => $invoiceNumber,
            'amount'         => $req['amount'],
        ];

        $res = SmsTransaction::create($arr);
        return $res;
    }

    /**
     * GET PAYMENT SUCCESS STATUS FROM SSL
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function paymentSuccess($request)
    {
        $smsTrans = SmsTransaction::where('invoice_number', $request->tran_id)->where('status', '!=', 'success')->first();
        $invArr = SmsTransaction::where('invoice_number', $request->tran_id)->first();

        // -----------trans update------------
        $data['status']        = 'success';
        $data['bank_trans_id'] = $request->bank_tran_id ?? "";
        $data['card_type']     = $request->card_type ?? "";
        $data['payment_date']  = date('Y-m-d');
        $data['paid_amount']   = $invArr->amount ?? "";
        $smsTransUpdate        = $invArr->update($data);

        // After Payment Success
        if (!empty($smsTrans) && !empty($smsTransUpdate)) {
            //-----------sms banalce update---------- 
            $siteSetting = SiteSetting::first();
            $newBalance  = (int) $siteSetting->sms_balance + (int) $smsTrans->amount;

            $siteSetting->update(['sms_balance' => $newBalance]);
            Cache::forget('site_setting_cache');
        }

        return response()->json(true);
    }
}
